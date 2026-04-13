<template>
    <div>
        <TheHeader />

        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260"
                @close="showSidebar = false" class="d-lg-none">
                <SidebarManagement />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarManagement />
                </div>

                <div class="col-12 col-lg-9">

                    <h1 class="mb-3 mt-2 mt-lg-3">Bảng điều khiển phòng ban</h1>

                    <div class="row g-3">

                        <div class="col-12 col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <i class="bi bi-people fs-1"></i>
                                        </div>
                                        <div class="col-9">
                                            <p>Tổng nhân viên phòng ban</p>
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
                                            <i class="bi bi-clock fs-1"></i>
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

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <h2 style="color: gray">Nhân viên gần đây</h2>
                        <a-button type="primary" @click="goEmployees">
                            Xem tất cả
                        </a-button>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Họ tên</th>
                                    <th>Vị trí</th>
                                    <th class="d-none d-lg-table-cell">Phòng ban</th>
                                    <th class="d-none d-lg-table-cell">Email</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.employee?.position }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ user.employee?.department }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ user.email }}
                                    </td>
                                    <td>{{ user.employee?.status }}</td>
                                </tr>

                                <tr v-if="users.length === 0">
                                    <td colspan="5" class="text-center">
                                        Không có nhân viên
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
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../services/http'

const users = ref([])
const totalUsers = ref(0)
const pendingLeaves = ref(0)
const showSidebar = ref(false)

const fetchDashboard = async () => {
    try {
        const res = await http.get('/management/dashboard')

        totalUsers.value = res.data.total
        users.value = res.data.users
        pendingLeaves.value = res.data.pending_leaves
    } catch (error) {
        console.log(error)
    }
}

onMounted(fetchDashboard)

const router = useRouter()

const goEmployees = () => {
    router.push('/management/employees')
}
</script>