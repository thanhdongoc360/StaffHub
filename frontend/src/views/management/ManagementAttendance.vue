<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarManagement />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarManagement />
                </div>

                <div class="col-12 col-lg-9">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Chấm công phòng ban</h1>
                    </div>

                    <!-- FILTER -->
                    <div class="mt-3 filter-panel">
                        <div class="d-flex flex-column flex-md-row flex-wrap gap-3">
                            <div class="filter-item filter-item-search">
                                <label class="form-label fw-semibold mb-1">Tìm kiếm</label>
                                <input v-model="search" placeholder="Tên / mã NV" class="form-control w-100"
                                    @keyup.enter="fetchData" />
                            </div>

                            <div class="filter-item filter-item-sm">
                                <label class="form-label fw-semibold mb-1">Tháng</label>
                                <select v-model="month" class="form-select w-100">
                                    <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                                </select>
                            </div>

                            <div class="filter-item filter-item-sm">
                                <label class="form-label fw-semibold mb-1">Năm</label>
                                <input v-model="year" type="number" class="form-control w-100" />
                            </div>

                            <div class="filter-item filter-item-sm">
                                <label class="form-label fw-semibold mb-1">Trạng thái</label>
                                <select v-model="status" class="form-select w-100">
                                    <option value="">Tất cả</option>
                                    <option value="present">Đi làm</option>
                                    <option value="late">Đi muộn</option>
                                    <option value="half_day">Nửa ngày</option>
                                </select>
                            </div>

                            <div class="filter-item filter-item-xs d-flex align-items-end">
                                <button class="btn btn-primary w-100" @click="fetchData">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive mt-4">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Mã NV</th>
                                    <th>Họ tên</th>
                                    <th class="d-none d-md-table-cell">Ngày</th>
                                    <th class="d-none d-lg-table-cell">Check-in</th>
                                    <th class="d-none d-lg-table-cell">Check-out</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in list" :key="item.id">
                                    <td>{{ item.employee_code }}</td>
                                    <td>{{ item.name }}</td>
                                    <td class="d-none d-md-table-cell">{{ item.date }}</td>
                                    <td class="d-none d-lg-table-cell">{{ item.check_in_time || '-' }}</td>
                                    <td class="d-none d-lg-table-cell">{{ item.check_out_time || '-' }}</td>
                                    <td>
                                        <span :class="statusClass(item.status)">{{ statusText(item.status) }}</span>
                                    </td>

                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" @click="openEdit(item)">
                                            Sửa
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="list.length === 0">
                                    <td colspan="7" class="text-center text-muted">Không có dữ liệu chấm công</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <a-modal v-model:open="isModalOpen" title="Sửa chấm công" ok-text="Lưu" cancel-text="Đóng"
                        @ok="save" @cancel="isModalOpen = false">
                        <div class="mb-2">
                            <label>Check-in</label>
                            <input v-model="form.check_in_time" class="form-control" />
                        </div>

                        <div class="mb-2">
                            <label>Check-out</label>
                            <input v-model="form.check_out_time" class="form-control" />
                        </div>

                        <div class="mb-2">
                            <label>Trạng thái</label>
                            <select v-model="form.status" class="form-select">
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="half_day">Half day</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Ghi chú</label>
                            <textarea v-model="form.note" class="form-control"></textarea>
                        </div>
                    </a-modal>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'
import { ref, onMounted } from 'vue'
import http from '../../services/http'

const list = ref([])
const search = ref('')
const month = ref(new Date().getMonth() + 1)
const year = ref(new Date().getFullYear())
const status = ref('')

const isModalOpen = ref(false)
const form = ref({})
const selectedId = ref(null)

const statusClass = (status) => {
    if (status === 'present') return 'status-present'
    if (status === 'late') return 'status-late'
    if (status === 'half_day') return 'status-half-day'
    return 'status-default'
}

const statusText = (status) => {
    if (status === 'present') return 'Đi làm'
    if (status === 'late') return 'Đi muộn'
    if (status === 'half_day') return 'Nửa ngày'
    return status || 'Không xác định'
}

const fetchData = async () => {
    const res = await http.get('/management/attendance', {
        params: {
            search: search.value,
            month: month.value,
            year: year.value,
            status: status.value
        }
    })

    list.value = res.data
}

const openEdit = (item) => {
    selectedId.value = item.id,
        form.value = { ...item }
    isModalOpen.value = true
}

const save = async () => {
    await http.put(`management/attendance/${selectedId.value}`, form.value)
    isModalOpen.value = false
    fetchData()
}

onMounted(fetchData)

</script>

<style scoped>
.filter-item {
    flex: 1 1 250px;
    min-width: 0;
}

.filter-item-search {
    flex: 0 1 520px;
}

.filter-item-sm {
    flex: 0 1 180px;
}

.filter-item-xs {
    flex: 0 1 150px;
}

.status-present {
    color: #198754;
    font-weight: 600;
}

.status-late {
    color: #fd7e14;
    font-weight: 600;
}

.status-half-day {
    color: #0d6efd;
    font-weight: 600;
}

.status-default {
    color: #6c757d;
    font-weight: 600;
}

@media (max-width: 576px) {
    .filter-item,
    .filter-item-search,
    .filter-item-sm,
    .filter-item-xs {
        flex-basis: 100%;
    }
}
</style>