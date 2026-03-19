<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarEmployee />
                </div>

                <div class="col-9">
                    <h1 class="mt-3">Quản lý lương của tôi</h1>

                    <a-form>
                        <a-select v-model:value="selectedMonth" placeholder="Chọn tháng" class="me-1">
                            <a-select-option v-for="m in 12" :key="m" :value="m">
                                Tháng {{ m }}
                            </a-select-option>
                        </a-select>

                        <a-input v-model:value="selectYear" placeholder="Nhập năm" style="width: 150px" class="me-3" />

                        <a-button type="primary" @click="searchSalary">Tìm kiếm</a-button>
                    </a-form>

                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Tháng/Năm</th>
                                <th scope="col">Lương cơ bản</th>
                                <th scope="col">Thưởng</th>
                                <th scope="col">Tổng</th>
                                <th scope="col">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="salary in salaries" :key="salary.id">
                                <td>{{ salary.month }}/{{ salary.year }}</td>
                                <td>{{ salary.base_salary }}</td>
                                <td>{{ salary.bonus }}</td>
                                <td>{{ salary.total }}</td>
                                <td>{{ salary.note }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarEmployee from '../../components/SidebarEmployee.vue';

import { ref, onMounted } from 'vue'
import axios from 'axios'

const salaries = ref([])

const selectedMonth = ref(null)
const selectedYear = ref(null)

const fetchSalaries = async () => {
    try {
        const token = localStorage.getItem('token')

        const res = await axios.get(
            'http://localhost:8000/api/my-salaries',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        salaries.value = res.data
    } catch (error) {
        console.log('Lỗi: ', error)
    }
}

const searchSalary = async () => {
    try {
        const token = localStorage.getItem('token')

        const res = await axios.get(
            'http://localhost:8000/api/my-salaries',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                },
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