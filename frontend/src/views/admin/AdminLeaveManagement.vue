<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarAdmin />
                </div>
                <div class="col-9">
                    <h1>Quản lý đơn nghỉ phép</h1>

                    <table class="table mt-4">
                        <thead>
                            <tr>
                                <th scope="col">Tên nhân viên</th>
                                <th scope="col">Loại nghỉ</th>
                                <th scope="col">Ngày bắt đầu</th>
                                <th scope="col">Ngày kết thúc</th>
                                <th scope="col">Lý do</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="leave in leaves" :key="leave.id">
                                <td>{{ leave.employee?.user?.name }}</td>
                                <td>{{ leave.type }}</td>
                                <td>{{ leave.start_date }}</td>
                                <td>{{ leave.end_date }}</td>
                                <td>{{ leave.reason }}</td>
                                <td>{{ leave.status }}</td>
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
                                <td colspan="7" class="text-center text-muted">Chưa có đơn nghỉ phép nào</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue'

const leaves = ref([])

const fetchLeaves = async () => {


    try {
        const token = localStorage.getItem('token')

        const res = await axios.get('http://localhost:8000/api/admin/leaves',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )
        leaves.value = res.data.data || []
    } catch (error) {
        console.log('Không tải được danh sách đơn nghỉ phép:', error)
        leaves.value = []
    }
}

const approve = async (id) => {
    try {
        const token = localStorage.getItem('token')

        await axios.post(`http://localhost:8000/api/admin/leaves/${id}/approve`,
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        );

        fetchLeaves()
    } catch (error) {
        console.log('Không duyệt được đơn nghỉ:', error)
    }
}

const reject = async (id) => {
    try {
        const token = localStorage.getItem('token')

        await axios.post(`http://localhost:8000/api/admin/leaves/${id}/reject`,
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )
        fetchLeaves()
    } catch (error) {
        console.log('Không từ chối được đơn nghỉ:', error)
    }
}

onMounted(() => {
    fetchLeaves()
})

</script>