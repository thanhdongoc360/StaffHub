<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarManagement />
            </a-drawer>
        </div>

        <div class="row">
            <div class="d-none d-lg-block col-lg-3">
                <SidebarManagement />
            </div>

            <div class="col-12 col-lg-9">
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                    <h1 class="mb-0">
                        Nhân viên phòng ban:
                        <span class="text-primary">{{ department }}</span>
                    </h1>

                    <a-button type="primary" @click="exportExcel"
                        style="background-color: #198754; border-color: #198754;" class="me-3">
                        <i class="fa-solid fa-file-excel me-2"></i>
                        Xuất Excel
                    </a-button>
                </div>

                <div class="mt-3">

                    <div class="d-flex flex-wrap align-items-end gap-3 mt-3">
                        <!-- Search -->
                        <div>
                            <label class="me-2 form-label fw-semibold">Tìm kiếm</label>
                            <a-input placeholder="Tên, email, mã NV..." style="width: 250px" v-model:value="search"
                                @pressEnter="handleSearch" allow-clear />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="me-2 form-label fw-semibold">Trạng thái</label>
                            <a-select style="width: 180px" v-model:value="status" @change="handleSearch" allow-clear>
                                <a-select-option value="">Tất cả</a-select-option>
                                <a-select-option value="active">Đang làm</a-select-option>
                                <a-select-option value="inactive">Nghỉ việc</a-select-option>
                            </a-select>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-end gap-3 mt-3">
                        <!-- Sort -->
                        <div>
                            <label class="me-2 form-label fw-semibold">Sắp xếp theo</label>
                            <a-select style="width: 180px" v-model:value="sortBy" @change="handleSearch">
                                <a-select-option value="id">ID</a-select-option>
                                <a-select-option value="name">Họ tên</a-select-option>
                                <a-select-option value="position">Vị trí</a-select-option>
                            </a-select>
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="me-2 form-label fw-semibold">Thứ tự</label>
                            <a-select style="width: 150px" v-model:value="sortOrder" @change="handleSearch">
                                <a-select-option value="asc">Tăng dần</a-select-option>
                                <a-select-option value="desc">Giảm dần</a-select-option>
                            </a-select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Họ tên</th>
                                <th>Vị trí</th>
                                <th class="d-none d-lg-table-cell">Email</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="employee in employees" :key="employee.id">
                                <td>{{ employee.user?.name }}</td>
                                <td>{{ employee.position }}</td>
                                <td class="d-none d-lg-table-cell">
                                    {{ employee.user?.email }}
                                </td>
                                <td>
                                    <span v-if="employee.status === 'active'" class="text-success">
                                        Đang làm
                                    </span>
                                    <span v-else class="text-danger">
                                        Nghỉ việc
                                    </span>
                                </td>
                                <td>
                                    <i class="fa-solid fa-eye" style="color:#00BFFF; cursor:pointer"
                                        @click="viewEmployee(employee)">
                                    </i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a-modal v-model:open="isViewModalOpen" ok-text="Đóng" :cancel-button-props="{ style: { display: 'none' } }"
            @ok="isViewModalOpen = false" @cancel="isViewModalOpen = false" title="Chi tiết nhân viên">
            <p><b>Họ và tên:</b> {{ selectedEmployee?.user?.name }}</p>
            <p><b>Email:</b> {{ selectedEmployee?.user?.email }}</p>
            <p><b>Mã nhân viên:</b> {{ selectedEmployee?.employee_code }}</p>
            <p><b>Vị trí:</b> {{ selectedEmployee?.position }}</p>
            <p><b>Phòng ban:</b> {{ selectedEmployee?.department }}</p>
            <p><b>Trạng thái:</b>
                <span v-if="selectedEmployee?.status === 'active'" class="text-success">
                    Đang làm
                </span>
                <span v-else class="text-danger">
                    Nghỉ việc
                </span>
            </p>
        </a-modal>
    </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarManagement from '../../components/SidebarManagement.vue';

import { ref, onMounted } from 'vue';
import http from '../../services/http'

const showSidebar = ref(false)

const employees = ref([])
const department = ref('')

const search = ref('')
const status = ref('')

const sortBy = ref('id')
const sortOrder = ref('desc')

const isViewModalOpen = ref(false)
const selectedEmployee = ref(null)

const fetchEmployees = async () => {
    try {
        const response = await http.get('/management/employees', {
            params: {
                search: search.value,
                status: status.value,
                sort_by: sortBy.value,
                sort_order: sortOrder.value
            }
        })

        employees.value = response.data.employees.data
        department.value = response.data.department

    } catch (error) {
        console.log(error)
    }
}

const handleSearch = () => {
    fetchEmployees()
}

const viewEmployee = (employee) => {
    selectedEmployee.value = employee
    isViewModalOpen.value = true
}

const exportExcel = async () => {
    try {
        const response = await http.get('/management/employees-export', {
            params: {
                search: search.value,
                status: status.value,
                sort_by: sortBy.value,
                sort_order: sortOrder.value
            },
            responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data])) // tao tam 1 url
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'employee.xlsx')
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
    } catch (error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchEmployees()
})

</script>