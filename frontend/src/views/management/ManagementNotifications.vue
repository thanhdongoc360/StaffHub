<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
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

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Thông báo phòng ban</h1>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <a-button
                                class="d-block d-sm-inline-block"
                                style="background-color: yellow; border-color: yellow;"
                                @click="filterType = 'unread'">
                                Chưa đọc
                            </a-button>

                            <a-button class="d-block d-sm-inline-block" @click="markAllAsRead">
                                <i class="fa-solid fa-check-double me-1"></i>
                                Đánh dấu tất cả đã đọc
                            </a-button>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Tiêu đề</th>
                                    <th class="d-none d-lg-table-cell">Nội dung</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th class="d-none d-lg-table-cell">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="noti in filteredNotifications" :key="noti.id">
                                    <td class="d-none d-md-table-cell">{{ noti.user?.name }}</td>
                                    <td>{{ noti.title }}</td>
                                    <td class="d-none d-lg-table-cell">{{ noti.content }}</td>
                                    <td>{{ formatDate(noti.created_at) }}</td>
                                    <td>
                                        <span v-if="!noti.is_read" class="text-danger">Chưa đọc</span>
                                        <span v-else class="text-success">Đã đọc</span>
                                    </td>
                                    <td>
                                        <a-button
                                            @click="markAsRead(noti)"
                                            :disabled="noti.is_read">
                                            Đánh dấu đã đọc
                                        </a-button>
                                    </td>
                                </tr>

                                <tr v-if="filteredNotifications.length === 0">
                                    <td colspan="6" class="text-center text-muted d-none d-lg-table-cell">
                                        Không có thông báo
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
import { ref, computed, onMounted } from 'vue'
import http from '../../services/http'
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'

const notifications = ref([])
const showSidebar = ref(false)
const filterType = ref('all')

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN')
}

const fetchNotifications = async () => {
    const res = await http.get('/notifications')
    notifications.value = res.data.data ?? []
}

const filteredNotifications = computed(() => {
    if (filterType.value === 'unread') {
        return notifications.value.filter(n => !n.is_read)
    }
    return notifications.value
})

const markAsRead = async (noti) => {
    if (noti.is_read) return

    await http.put(`/notifications/${noti.id}/read`)
    noti.is_read = true
}

const markAllAsRead = async () => {
    await http.put('/notifications/read-all')
    notifications.value.forEach(n => n.is_read = true)
}

onMounted(fetchNotifications)
</script>