<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarAccountant />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAccountant />
                </div>

                <div class="col-12 col-lg-9">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Danh sách bảng lương</h1>

                        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
                            <button class="btn btn-primary d-block d-sm-inline-block" @click="exportExcel">
                                Xuất Excel
                            </button>

                            <button class="btn btn-success d-block d-sm-inline-block" @click="createSalary">
                                Tạo bảng lương tháng
                            </button>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="mt-3">
                        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-items-stretch align-items-sm-center">

                            <input v-model="search" placeholder="Tìm kiếm theo tên" class="form-control w-100"
                                style="max-width: 220px;" />

                            <!-- Month -->
                            <select v-model="month" class="form-select w-100" style="max-width: 150px;">
                                <option v-for="m in 12" :key="m" :value="m">
                                    Tháng {{ m }}
                                </option>
                            </select>

                            <!-- Year -->
                            <select v-model="year" class="form-select w-100" style="max-width: 150px;">
                                <option :value="2025">2025</option>
                                <option :value="2026">2026</option>
                            </select>

                            <select v-model="status" class="form-select w-100" style="max-width: 220px;">
                                <option value="">Tất cả</option>
                                <option value="draft">Nháp</option>
                                <option value="calculated">Đã tính</option>
                                <option value="approved">Đã duyệt</option>
                                <option value="published">Đã công bố</option>
                            </select>

                            <button class="btn btn-primary" @click="loadSalaries">
                                Tìm kiếm
                            </button>
                        </div>
                    </div>


                    <!-- Action buttons -->
                    <div class="mt-3">
                        <div class="d-flex flex-column flex-sm-row gap-2 flex-wrap">
                            <button class="btn btn-success d-block d-sm-inline-block" @click="calculate">
                                Tính lương toàn bộ
                            </button>

                            <button class="btn btn-warning d-block d-sm-inline-block" @click="approve">
                                Duyệt toàn bộ
                            </button>

                            <button class="btn btn-dark d-block d-sm-inline-block" @click="publish">
                                Công bố toàn bộ
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nhân viên</th>
                                        <th>Tổng lương</th>
                                        <th class="d-none d-md-table-cell">Tháng/Năm</th>
                                        <th>Trạng thái</th>
                                        <th class="d-none d-lg-table-cell">Hành động</th>
                                        <th>Thực hiện</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="s in salaries" :key="s.id">
                                        <td class="text-nowrap">{{ s.user_name }}</td>
                                        <td>{{ s.total }}</td>
                                        <td class="d-none d-md-table-cell">{{ s.month }} / {{ s.year }}</td>
                                        <td>
                                            <span v-if="s.status === 'draft'" class="badge bg-secondary">
                                                Nháp
                                            </span>

                                            <span v-else-if="s.status === 'calculated'" class="badge bg-info">
                                                Đã tính
                                            </span>

                                            <span v-else-if="s.status === 'approved'" class="badge bg-warning">
                                                Đã duyệt
                                            </span>

                                            <span v-else class="badge bg-success">
                                                Đã công bố
                                            </span>
                                        </td>

                                        <td class="d-none d-lg-table-cell">
                                            <div class="d-flex flex-column flex-xl-row gap-2">
                                                <button class="btn btn-success btn-sm" @click="calculateOne(s.id)"
                                                    :disabled="s.status !== 'draft'">
                                                    Tính
                                                </button>

                                                <button class="btn btn-warning btn-sm" @click="approveOne(s.id)"
                                                    :disabled="s.status !== 'calculated'">
                                                    Duyệt
                                                </button>

                                                <button class="btn btn-dark btn-sm" @click="publishOne(s.id)"
                                                    :disabled="s.status !== 'approved'">
                                                    Công bố
                                                </button>
                                            </div>
                                        </td>

                                        <td>
                                            <a-space>
                                                <!-- View -->
                                                <i class="fa-solid fa-eye text-info" style="cursor: pointer"
                                                    @click="viewSalary(s.id)">
                                                </i>

                                                <!-- Edit -->
                                                <i class="fa-solid fa-pen-to-square text-primary"
                                                    style="cursor: pointer" @click="editSalary(s.id)">
                                                </i>
                                            </a-space>
                                        </td>
                                    </tr>

                                    <tr v-if="salaries.length === 0">
                                        <td colspan="6" class="text-center text-muted">
                                            Không có dữ liệu lương
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <a-modal v-model:open="showModal" title="Chi tiết / chỉnh sửa lương" @ok="saveSalary" :ok-button-props="{
            disabled: isLocked,
            style: { display: mode === 'view' ? 'none' : 'inline-block' }
        }">
            <div v-if="selectedSalary">

                <p><b>Nhân viên:</b> {{ selectedSalary.employee.user.name }}</p>
                <p><b>Tháng:</b> {{ selectedSalary.month }} / {{ selectedSalary.year }}</p>

                <p>
                    <b>Trạng thái:</b>
                    <span class="badge bg-info">{{ selectedSalary.status }}</span>
                </p>

                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label>Lương cơ bản</label>
                        <input v-model="selectedSalary.base_salary" type="number" class="form-control"
                            :disabled="isLocked" />
                    </div>

                    <div class="col-12">
                        <label>Thưởng</label>
                        <input v-model="selectedSalary.bonus" type="number" class="form-control" :disabled="isLocked" />
                    </div>

                    <div class="col-12">
                        <label>Thuế</label>
                        <input v-model="selectedSalary.tax" type="number" class="form-control" :disabled="isLocked" />
                    </div>

                    <div class="col-12">
                        <label>Ghi chú</label>
                        <textarea v-model="selectedSalary.note" class="form-control" :disabled="isLocked"></textarea>
                    </div>
                </div>

                <!-- TOTAL -->
                <div class="mt-3">
                    <h4>
                        Total:
                        <span class="text-success">{{ total }}</span>
                    </h4>
                </div>

            </div>
        </a-modal>
    </div>
</template>


<script setup>
import TheHeader from '../../components/TheHeader.vue'
import SidebarAccountant from '../../components/SidebarAccountant.vue'
import { ref, onMounted, computed } from 'vue'
import http from '../../services/http'

const showSidebar = ref(false)

const showModal = ref(false)

const salaries = ref([])
const month = ref(4)
const year = ref(2026)

const search = ref('')
const status = ref('')

const selectedSalary = ref(null)

const mode = ref('view') // 'view' | 'edit'

const loadSalaries = async () => {
    const res = await http.get('/accountant/salaries', {
        params: {
            month: month.value,
            year: year.value,
            search: search.value,
            status: status.value
        }
    })

    salaries.value = res.data.data
}

const calculate = async () => {
    await http.post('/accountant/salary/calculate', {
        month: month.value,
        year: year.value
    })
    loadSalaries()
}

const approve = async () => {
    await http.post('/accountant/salary/approve', {
        month: month.value,
        year: year.value
    })

    loadSalaries()
}

const publish = async () => {
    await http.post('/accountant/salary/publish', {
        month: month.value,
        year: year.value
    })
    loadSalaries()
}

const viewSalary = async (id) => {
    const res = await http.get(`/accountant/salary/${id}`)
    selectedSalary.value = res.data
    mode.value = 'view'
    showModal.value = true
}

const editSalary = async (id) => {
    const res = await http.get(`/accountant/salary/${id}`)
    selectedSalary.value = res.data
    mode.value = 'edit'
    showModal.value = true
}

const total = computed(() => {
    if (!selectedSalary.value) return 0

    return (
        Number(selectedSalary.value.base_salary || 0) +
        Number(selectedSalary.value.bonus || 0) -
        Number(selectedSalary.value.tax || 0)
    )
})

const isLocked = computed(() => {
    return (
        selectedSalary.value?.status === 'published' ||
        mode.value === 'view'
    )
})

const saveSalary = async () => {
    try {
        const res = await http.put(
            `/accountant/salary/${selectedSalary.value.id}`,
            selectedSalary.value
        )

        selectedSalary.value = res.data

        alert('Cập nhật thành công')

        showModal.value = false
        loadSalaries()
    } catch (error) {
        alert(error.response?.data?.message || 'Lỗi')
    }
}

const calculateOne = async (id) => {
    await http.post(`/accountant/salary/${id}/calculate`)
    loadSalaries()
}

const approveOne = async (id) => {
    await http.post(`/accountant/salary/${id}/approve`)
    loadSalaries()
}

const publishOne = async (id) => {
    await http.post(`/accountant/salary/${id}/publish`)
    loadSalaries()
}

const exportExcel = async () => {
    try {
        const res = await http.post(
            '/accountant/salary/export',
            {
                month: month.value,
                year: year.value
            },
            {
                responseType: 'blob'
            }
        )

        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')

        link.href = url
        link.setAttribute(
            'download',
            `salary_${month.value}_${year.value}.xlsx`
        )

        document.body.appendChild(link)
        link.click()

        link.remove()
    } catch (error) {
        alert('Xuất file thất bại')
    }
}

const createSalary = async () => {
    if (!confirm(`Tạo bảng lương tháng ${month.value}/${year.value}?`)) {
        return
    }

    try {
        await http.post('/accountant/salary/create', {
            month: month.value,
            year: year.value
        })

        alert('Tạo bảng lương thành công')
        loadSalaries()
    } catch (error) {
        alert(error.response?.data?.message || 'Lỗi')
    }
}

onMounted(() => {
    loadSalaries()
})

</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>