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
                    <h1 class="mt-2 mt-lg-3">Quản lý đơn nghỉ phép</h1>

                    <div class="table-responsive mt-4">
                        <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Tên nhân viên</th>
                                <th scope="col">Loại nghỉ</th>
                                <th class="d-none d-md-table-cell" scope="col">Ngày bắt đầu</th>
                                <th class="d-none d-md-table-cell" scope="col">Ngày kết thúc</th>
                                <th class="d-none d-lg-table-cell" scope="col">Lý do</th>
                                <th class="d-none d-lg-table-cell" scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="leave in leaves" :key="leave.id">
                                <td>{{ leave.employee?.user?.name }}</td>
                                <td>{{ leave.type }}</td>
                                <td class="d-none d-md-table-cell">{{ leave.start_date }}</td>
                                <td class="d-none d-md-table-cell">{{ leave.end_date }}</td>
                                <td class="d-none d-lg-table-cell">{{ leave.reason }}</td>
                                <td class="d-none d-lg-table-cell">
                                    <span :class="statusClass(leave.status)">{{ statusText(leave.status) }}</span>
                                </td>
                                <td>
                                    <template v-if="leave.status === 'Chờ duyệt'">
                                        <a-button class="me-2" type="primary" @click="approve(leave.id)">
                                            <i class="fa-solid fa-check me-1"></i>Duyệt
                                        </a-button>

                                        <a-button type="primary" danger @click="reject(leave.id)">
                                            <i class="fa-solid fa-x me-1"></i>Từ chối
                                        </a-button>
                                    </template>

                                    <template v-else-if="leave.status === 'Đã duyệt'">
                                        <span class="text-success fw-bold">Đã duyệt</span>
                                    </template>

                                    <template v-else-if="leave.status === 'Từ chối'">
                                        <span class="text-danger fw-bold">Đã từ chối</span>
                                    </template>
                                </td>
                            </tr>
                            <tr v-if="leaves.length === 0">
                                <td colspan="8" class="text-center text-muted">Chưa có đơn nghỉ phép nào</td>
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
import { ref, onMounted } from 'vue'
import http from "../../services/http";
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue'

const leaves = ref([])
const showSidebar = ref(false)

const fetchLeaves = async () => {
    try {
        const res = await http.get('/admin/leaves')
        leaves.value = res.data.data || []
    } catch (error) {
        console.log('Không tải được danh sách đơn nghỉ phép:', error)
        leaves.value = []
    }
}

const approve = async (id) => {
    try {
        await http.post(`/admin/leaves/${id}/approve`);

        fetchLeaves()
    } catch (error) {
        console.log('Không duyệt được đơn nghỉ:', error)
    }
}

const reject = async (id) => {
    try {
        await http.post(`/admin/leaves/${id}/reject`)
        fetchLeaves()
    } catch (error) {
        console.log('Không từ chối được đơn nghỉ:', error)
    }
}

const statusText = (status) => {
    if (status === 'Chờ duyệt') return 'Chờ duyệt'
    if (status === 'Đã duyệt') return 'Đã duyệt'
    if (status === 'Từ chối') return 'Từ chối'
    return status || 'Không xác định'
}

const statusClass = (status) => {
    if (status === 'Chờ duyệt') return 'text-warning fw-bold'
    if (status === 'Đã duyệt') return 'text-success fw-bold'
    if (status === 'Từ chối') return 'text-danger fw-bold'
    return 'text-muted fw-bold'
}

onMounted(() => {
    fetchLeaves()
})

</script>