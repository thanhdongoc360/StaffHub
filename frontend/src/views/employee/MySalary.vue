<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false" class="d-lg-none">
                <SidebarEmployee />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarEmployee />
                </div>

                <div class="col-12 col-lg-9">
                    <h1 class="mt-3">Quản lý lương của tôi</h1>

                    <a-form class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
                        <a-select v-model:value="selectedMonth" placeholder="Chọn tháng" class="w-100" style="max-width: 220px;">
                            <a-select-option v-for="m in 12" :key="m" :value="m">
                                Tháng {{ m }}
                            </a-select-option>
                        </a-select>

                        <a-input v-model:value="selectedYear" placeholder="Nhập năm" class="w-100" style="max-width: 220px;" />

                        <a-button type="primary" @click="searchSalary" class="d-block d-sm-inline-block">Tìm kiếm</a-button>
                    </a-form>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Tháng/Năm</th>
                                    <th scope="col">Lương cơ bản</th>
                                    <th scope="col">Thưởng</th>
                                    <th scope="col">Tổng</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="salary in salaries" :key="salary.id">
                                    <td>{{ salary.month }}/{{ salary.year }}</td>
                                    <td>{{ salary.base_salary }}</td>
                                    <td>{{ salary.bonus }}</td>
                                    <td>{{ salary.total }}</td>
                                    <td class="d-none d-lg-table-cell">{{ salary.note }}</td>
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
import TheHeader from '../../components/TheHeader.vue';
import SidebarEmployee from '../../components/SidebarEmployee.vue';

import { ref, onMounted } from 'vue'
import http from "../../services/http";

const salaries = ref([])
const showSidebar = ref(false)

const selectedMonth = ref(null)
const selectedYear = ref(null)

const fetchSalaries = async () => {
    try {
        const res = await http.get(
            '/my-salaries'
        )

        salaries.value = res.data
    } catch (error) {
        console.log('Lỗi: ', error)
    }
}

const searchSalary = async () => {
    try {
        const res = await http.get(
            '/my-salaries',
            {
                params: {
                    month: selectedMonth.value,
                    year: selectedYear.value
                }
            }
        )

        salaries.value = res.data

        if (!selectedMonth.value && !selectedYear.value) {
            fetchSalaries()
            return
        }
    } catch (error) {
        console.log('Lỗi: ', error)
    }
}

onMounted(() => {
    fetchSalaries()
})

</script>