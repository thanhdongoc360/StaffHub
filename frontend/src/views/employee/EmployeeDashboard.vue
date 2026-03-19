<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarEmployee />
                </div>
                <div class="col-9">
                    <div class="row">
                        <div class="col-10">
                            <h1>Bảng điều khiển</h1>
                        </div>
                        <div class="col-2 mt-3">
                            <a-button>Xem hồ sơ</a-button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 border border-secondary">  
                            <h2>Thông tin cá nhân</h2>
                            <p>Họ tên: {{ userStore.user?.name }}</p>
                            <p>Email: {{ userStore.user?.email }}</p>
                            <p>Vị trí: {{ userStore.user?.employee.position }}</p>
                            <p>Phòng ban: {{ userStore.user?.employee.department }}</p>
                            <p>Trạng thái: <span style="background-color: green; color: white; padding: 8px; border-radius:8px;">{{
                                    userStore.user?.employee.status }}</span></p>
                        </div>
                        <div class="col-8 border border-secondary">
                            <h2>Lịch làm việc</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarEmployee from '../../components/SidebarEmployee.vue';
import { onMounted } from 'vue';
import axios from 'axios'

import { useUserStore } from '../../stores/user';

const userStore = useUserStore()

onMounted(async () => {
    try {
        if (!userStore.user) {
            const cachedUser = localStorage.getItem('user')
            if (cachedUser) {
                userStore.setUser(JSON.parse(cachedUser))
            }
        }

        const token = localStorage.getItem('token')

        if (!userStore.user && token) {
            const response = await axios.get('http://localhost:8000/api/user', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })

            userStore.setUser(response.data)
            localStorage.setItem('user', JSON.stringify(response.data))
        }

        console.log('employee info:', userStore.user)
    } catch (error) {
        console.log('Không lấy được thông tin employee:', error?.response?.data || error)
    }
})

</script>