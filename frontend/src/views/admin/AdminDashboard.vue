<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarAdmin />
                </div>
                <div class="col-9">

                    <h1 class="mb-3">Bảng điều khiển</h1>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-4">
                                <div class="card" style="width: 18rem;">
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

                            <div class="col-4">
                                <div class="card" style="width: 18rem;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <i class="bi bi-person-circle fs-1"></i>
                                            </div>
                                            <div class="col-9">
                                                <p>Đơn nghỉ chờ duyệt</p>
                                                <h2>5</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="card" style="width: 18rem;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <i class="bi bi-person-circle fs-1"></i>
                                            </div>
                                            <div class="col-9">
                                                <p>Dự án đang hoạt động</p>
                                                <h2>9</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-10">
                            <h2 style="color: gray">Nhân viên gần đây</h2>
                        </div>
                        <div class="col-2 mt-3">
                            <a-button type="primary" @click="employeeManagement">Xem tất cả nhân viên</a-button>
                        </div>
                    </div>
                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Họ tên</th>
                                <th scope="col">Vị trí</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Email</th>
                                <th scope="col">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user) in users" :key="user.id">
                                <td>{{ user.name }}</td>
                                <td>{{ user.employee?.position }}</td>
                                <td>{{ user.employee?.department }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.employee?.status }}</td>
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
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue';
import axios from 'axios'

const users = ref([])
const totalUsers = ref(0)

const fetchUsers = async () => {
    const token = localStorage.getItem('token')

    try {
        const response = await axios.get('http://127.0.0.1:8000/api/admin/dashboard',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        totalUsers.value = response.data.total;
        users.value = response.data.users;
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