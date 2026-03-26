<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarAdmin />
                </div>

                <div class="col-9">
                    <div class="row">
                        <div class="col-10">
                            <h1 class="mt-3">Quản lý lương</h1>
                        </div>

                        <div class="col-2 mt-5">
                            <a-button type="primary" @click="showModal = true">
                                <i class="fa-solid fa-plus me-1"></i>Thêm lương
                            </a-button>
                        </div>
                    </div>


                    <a-form>
                        <a-select v-model:value="selectedMonth" placeholder="Chọn tháng" class="me-1">
                            <a-select-option v-for="m in 12" :key="m" :value="m">
                                Tháng {{ m }}
                            </a-select-option>
                        </a-select>

                        <a-input v-model:value="selectedYear" placeholder="Nhập năm" style="width: 150px"
                            class="me-3" />

                        <a-button type="primary" @click="searchSalary">Tìm kiếm</a-button>
                    </a-form>

                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Tên nhân viên</th>
                                <th scope="col">Tháng/Năm</th>
                                <th scope="col">Lương cơ bản</th>
                                <th scope="col">Thưởng</th>
                                <th scope="col">Tổng</th>
                                <th scope="col">Ghi chú</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="salary in salaries" :key="salary.id">
                                <td>{{ salary.employee.user.name }}</td>
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