<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false" class="d-lg-none">
                <SidebarAdmin />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAdmin />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                                <h1 class="mb-0">Quản lý lương</h1>
                                <a-button type="primary" @click="showModal = true" class="d-block d-sm-inline-block">
                                    <i class="fa-solid fa-plus me-1"></i>Thêm lương
                                </a-button>
                            </div>
                        </div>
                    </div>

                    <a-form class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center mt-3">
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
                                    <th scope="col">Tên nhân viên</th>
                                    <th scope="col">Tháng/Năm</th>
                                    <th scope="col">Lương cơ bản</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Thưởng</th>
                                    <th scope="col">Tổng</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Ghi chú</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="salary in salaries" :key="salary.id">
                                    <td>{{ salary.employee.user.name }}</td>
                                    <td>{{ salary.month }}/{{ salary.year }}</td>
                                    <td>{{ salary.base_salary }}</td>
                                    <td class="d-none d-lg-table-cell">{{ salary.bonus }}</td>
                                    <td>{{ salary.total }}</td>
                                    <td class="d-none d-lg-table-cell">{{ salary.note }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <a-modal v-model:open="showModal" title="Thêm lương" @ok="handleSubmit">
            <a-select v-model:value="form.employee_id" placeholder="Chọn nhân viên" style="width:100%">
                <a-select-option v-for="emp in employees" :key="emp.id" :value="emp.id">
                    {{ emp.user.name }}
                </a-select-option>
            </a-select>

            <a-input v-model:value="form.month" placeholder="Tháng" class="mt-2" />
            <a-input v-model:value="form.year" placeholder="Năm" class="mt-2" />
            <a-input v-model:value="form.base_salary" placeholder="Lương cơ bản" class="mt-2" />
            <a-input v-model:value="form.bonus" placeholder="Thưởng" class="mt-2" />
            <a-input v-model:value="form.note" placeholder="Ghi chú" class="mt-2" />
        </a-modal>
    </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { ref, onMounted } from 'vue'
import http from "../../services/http";

const salaries = ref([])
const showSidebar = ref(false)

const selectedMonth = ref(null)
const selectedYear = ref(null)

const showModal = ref(false)

const form = ref({
    employee_id: null,
    month: null,
    year: null,
    base_salary: null,
    bonus: null,
    note: null
})

const employees = ref([])

const fetchEmployees = async () => {
    const res = await http.get('/admin/employees')

    employees.value = res.data
}

const fetchAllSalaries = async () => {
    try {
        const res = await http.get('/admin/salaries')

        salaries.value = res.data
    } catch (error) {
        console.log(error)
    }
}

const handleSubmit = async () => {
    try {
        const res = await http.post(
            '/admin/salaries',
            form.value)

        salaries.value.unshift(res.data)

        showModal.value = false
    } catch (error) {
        console.log(error.response.data)
    }
}

const searchSalary = async () => {
    try {
        if (!selectedMonth.value || !selectedYear.value) {
            alert("Vui lòng chọn tháng và nhập năm")
            return
        }

        const res = await http.get(`/admin/salaries?month=${selectedMonth.value}&year=${selectedYear.value}`)

        salaries.value = res.data
    } catch (error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchAllSalaries()
    fetchEmployees()
})


</script>