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

                    <h1 class="mb-3 mt-2 mt-lg-3">Bảng điều khiển</h1>

                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <i class="bi bi-person-circle fs-1"></i>
                                            </div>
                                            <div class="col-9">
                                                <p>Tổng nhân viên</p>
                                                <h2>{{ totalUsers }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <i class="bi bi-person-circle fs-1"></i>
                                            </div>
                                            <div class="col-9">
                                                <p>Đơn nghỉ chờ duyệt</p>
                                                <h2>{{ pendingLeaves }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid mt-4">
                        <div class="row">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2">
                                <h2 style="color: gray" class="mb-0">Nhân viên gần đây</h2>
                                <a-button type="primary" @click="employeeManagement" class="d-block d-sm-inline-block">Xem tất cả nhân viên</a-button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Họ tên</th>
                                    <th scope="col">Vị trí</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Phòng ban</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Email</th>
                                    <th scope="col">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user) in users" :key="user.id">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.employee?.position }}</td>
                                    <td class="d-none d-lg-table-cell">{{ user.employee?.department }}</td>
                                    <td class="d-none d-lg-table-cell">{{ user.email }}</td>
                                    <td>{{ user.employee?.status }}</td>
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
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue';
import http from "../../services/http";

const users = ref([])
const totalUsers = ref(0)
const showSidebar = ref(false)

const pendingLeaves = ref(0)

const fetchUsers = async () => {
    try {
        const response = await http.get('/admin/dashboard')

        totalUsers.value = response.data.total;
        users.value = response.data.users;
        pendingLeaves.value = response.data.pending_leaves;
    }
    catch (error) {
        console.log(error);
    }
}

onMounted(() => {
    fetchUsers()
})

const router = useRouter()

const employeeManagement = async () => {
    try {
        router.push('/admin/employees')
    }
    catch (error) {
        console.log(error)
    }
}


</script>