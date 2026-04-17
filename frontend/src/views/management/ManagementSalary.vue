<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer
                :visible="showSidebar"
                placement="left"
                width="260"
                @close="showSidebar = false"
                class="d-lg-none"
            >
                <SidebarManagement />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarManagement />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Quản lý lương phòng ban</h1>
                    </div>

                    <!-- FILTER -->
                    <a-form class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-items-stretch align-items-sm-center mt-3">
                        <a-select
                            :value="selectedMonth"
                            @update:value="selectedMonth = $event"
                            placeholder="Chọn tháng"
                            class="w-100"
                            style="max-width: 220px"
                        >
                            <a-select-option v-for="m in 12" :key="m" :value="m">
                                Tháng {{ m }}
                            </a-select-option>
                        </a-select>

                        <a-input
                            :value="selectedYear"
                            @update:value="selectedYear = $event"
                            placeholder="Nhập năm"
                            class="w-100"
                            style="max-width: 220px"
                        />

                        <a-button type="primary" @click="searchSalary" class="d-block d-sm-inline-block">
                            Tìm kiếm
                        </a-button>
                    </a-form>

                    <!-- TABLE -->
                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Tháng/Năm</th>
                                    <th>Lương cơ bản</th>
                                    <th class="d-none d-lg-table-cell">Thưởng</th>
                                    <th>Tổng</th>
                                    <th class="d-none d-lg-table-cell">Ghi chú</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="salary in salaries" :key="salary.id">
                                    <td>{{ salary.employee?.user?.name }}</td>
                                    <td>{{ salary.month }}/{{ salary.year }}</td>
                                    <td>{{ salary.base_salary }}</td>
                                    <td class="d-none d-lg-table-cell">{{ salary.bonus }}</td>
                                    <td>{{ salary.total }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ salary.note }}
                                    </td>
                                </tr>

                                <tr v-if="salaries.length === 0">
                                    <td colspan="6" class="text-center text-muted d-none d-lg-table-cell">
                                        Không có dữ liệu
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import http from '../../services/http'
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'

const salaries = ref([])
const showSidebar = ref(false)

const selectedMonth = ref(null)
const selectedYear = ref(null)

const fetchSalaries = async () => {
    try {
        const res = await http.get('/management/salaries')
        salaries.value = res.data
    } catch (error) {
        console.log(error)
    }
}

const searchSalary = async () => {
    try {
        const res = await http.get('/management/salaries', {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value
            }
        })

        salaries.value = res.data
    } catch (error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchSalaries()
})
</script>