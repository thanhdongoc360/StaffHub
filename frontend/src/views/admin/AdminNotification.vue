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
                        <div class="col-8">
                            <h1 class="mt-3">Thông báo</h1>
                        </div>

                        <div class="col-4 mt-5">
                            <a-button class="me-2" style="background-color: yellow; border-color: yellow;"
                                @click="filterType = 'unread'">Chưa
                                đọc</a-button>
                            <a-button @click="markAllAsRead">
                                <i class="fa-solid fa-check-double me-1"></i>Đánh dấu là đã đọc
                            </a-button>
                        </div>
                    </div>

                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Nhân viên</th>
                                <th scope="col">Tiêu đề</th>
                                <th scope="col">Nội dung</th>
                                <th scope="col">Thời gian</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="noti in filteredNotifications" :key="noti.id">
                                <td>{{ noti.user?.name }}</td>
                                <td>{{ noti.title }}</td>
                                <td>{{ noti.content }}</td>
                                <td>{{ noti.date }}</td>
                                <td>
                                    <span v-if="!noti.is_read" class="text-danger">Chưa đọc</span>
                                    <span v-else class="text-success">Đã đọc</span>
                                </td>
                                <td>
                                    <a-button @click="markAsRead(noti)" :disabled="noti.is_read"
                                        :class="{ 'opacity-50': noti.is_read }">Đánh dấu là đã đọc</a-button>
                                </td>
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

import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const notifications = ref([])
const filterType = ref('all')

const fetchNotifications = async () => {
    try {
        const token = localStorage.getItem('token')

        const res = await axios.get(
            'http://localhost:8000/api/notifications',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        notifications.value = res.data.data ?? []
    } catch (error) {
        console.log('Lỗi tải thông báo', error)
    }
}

onMounted(() => {
    fetchNotifications()
})

const filteredNotifications = computed(() => {
    if (filterType.value === 'unread') {
        return notifications.value.filter(noti => !noti.is_read)
    }

    return notifications.value
})

const markAsRead = async (noti) => {
    if (noti.is_read) return

    try {
        const token = localStorage.getItem('token')

        await axios.put(
            `http://localhost:8000/api/notifications/${noti.id}/read`,
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        noti.is_read = true
    } catch (error) {
        console.log('Lỗi cập nhật trạng thái', error)
    }
}

const markAllAsRead = async () => {
    try {
        const token = localStorage.getItem('token')

        await axios.put(
            'http://localhost:8000/api/notifications/read-all',
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        notifications.value.forEach(noti => {
            noti.is_read = true
        })
    } catch (error) {
        console.log('Lỗi đọc tất cả', error)
    }
}


</script>