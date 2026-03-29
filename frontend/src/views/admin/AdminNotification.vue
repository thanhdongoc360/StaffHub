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
                                <h1 class="mb-0">Thông báo</h1>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a-button style="background-color: yellow; border-color: yellow;" @click="filterType = 'unread'">Chưa đọc</a-button>
                                    <a-button @click="markAllAsRead">
                                        <i class="fa-solid fa-check-double me-1"></i>Đánh dấu là đã đọc
                                    </a-button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="d-none d-lg-table-cell" scope="col">Nhân viên</th>
                                    <th scope="col">Tiêu đề</th>
                                    <th class="d-none d-lg-table-cell" scope="col">Nội dung</th>
                                    <th scope="col">Thời gian</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="noti in filteredNotifications" :key="noti.id">
                                    <td class="d-none d-lg-table-cell">{{ noti.user?.name }}</td>
                                    <td>{{ noti.title }}</td>
                                    <td class="d-none d-lg-table-cell">{{ noti.content }}</td>
                                    <td>{{ formatDate(noti.date) }}</td>
                                    <td>
                                        <span v-if="!noti.is_read" class="text-danger">Chưa đọc</span>
                                        <span v-else class="text-success">Đã đọc</span>
                                    </td>
                                    <td>
                                        <a-button @click="markAsRead(noti)" :disabled="noti.is_read" :class="{ 'opacity-50': noti.is_read }">Đánh dấu là đã đọc</a-button>
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
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { ref, onMounted, computed } from 'vue'
import http from "../../services/http";

const notifications = ref([])
const showSidebar = ref(false)
const filterType = ref('all')

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN')
}

const fetchNotifications = async () => {
    try {
        const res = await http.get('/notifications')

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
        await http.put(`/notifications/${noti.id}/read`)

        noti.is_read = true
    } catch (error) {
        console.log('Lỗi cập nhật trạng thái', error)
    }
}

const markAllAsRead = async () => {
    try {
        await http.put('/notifications/read-all')

        notifications.value.forEach(noti => {
            noti.is_read = true
        })
    } catch (error) {
        console.log('Lỗi đọc tất cả', error)
    }
}


</script>