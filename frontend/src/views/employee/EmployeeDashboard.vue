<template>
    <div>
        <!-- Header -->
        <TheHeader />

        <div class="container-fluid mt-3">
            <!-- Nút mở sidebar cho mobile -->
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <!-- Sidebar drawer cho mobile -->
            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarEmployee />
            </a-drawer>

            <div class="row">
                <!-- Sidebar desktop -->
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarEmployee />
                </div>

                <!-- Nội dung chính -->
                <div class="col-12 col-lg-9">
                    <!-- Header bảng điều khiển -->
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Bảng điều khiển</h1>
                        <a-button type="primary" class="d-block d-sm-inline-block" @click="goProfile">
                            Xem hồ sơ
                        </a-button>
                    </div>

                    <!-- Các card thông tin -->
                    <div class="row mt-4 g-3">
                        <!-- Thông tin cá nhân -->
                        <div class="col-12 col-xl-4">
                            <div class="card p-3 h-100">
                                <h2 class="mb-3">Thông tin cá nhân</h2>
                                <p><strong>Họ tên:</strong> {{ userStore.user?.name }}</p>
                                <p><strong>Email:</strong> {{ userStore.user?.email }}</p>
                                <p><strong>Vị trí:</strong> {{ userStore.user?.employee.position }}</p>
                                <p><strong>Phòng ban:</strong> {{ userStore.user?.employee.department }}</p>
                                <p>
                                    <strong>Trạng thái:</strong>
                                    <span
                                        :class="['status-badge', employeeStatusClass(userStore.user?.employee.status)]">
                                        {{ formatStatus(userStore.user?.employee.status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Lịch làm việc -->
                        <div class="col-12 col-xl-8">
                            <div class="card p-3 h-100">
                                <h2 class="mb-3">Lịch làm việc</h2>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h2 class="mb-0">Lịch sử đơn nghỉ phép</h2>
                                    <a-button type="primary" @click="goToLeaves">Xem đơn nghỉ phép</a-button>
                                </div>

                                <div class="table-responsive mt-2">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Ngày bắt đầu</th>
                                                <th class="d-none d-md-table-cell" scope="col">Ngày kết thúc</th>
                                                <th class="d-none d-lg-table-cell" scope="col">Số ngày</th>
                                                <th scope="col">Loại nghỉ</th>
                                                <th class="d-none d-lg-table-cell" scope="col">Lý do</th>
                                                <th scope="col">Trạng thái</th>
                                                <th class="d-none d-lg-table-cell" scope="col">Ngày nộp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="leave in leaveData" :key="leave.id">
                                                <td>{{ leave.start_date }}</td>
                                                <td class="d-none d-md-table-cell">{{ leave.end_date }}</td>
                                                <td class="d-none d-lg-table-cell">
                                                    {{ Math.ceil((new Date(leave.end_date) - new Date(leave.start_date))
                                                        / (1000 * 60 * 60 * 24)) + 1 }}
                                                </td>
                                                <td>{{ leave.type }}</td>
                                                <td class="d-none d-lg-table-cell">{{ leave.reason }}</td>
                                                <td>
                                                    <span :class="statusClass(leave.status)">{{ leave.status }}</span>
                                                </td>
                                                <td class="d-none d-lg-table-cell">{{ leave.created_at }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
import { ref, onMounted } from 'vue';
import http from "../../services/http";
import { useUserStore } from '../../stores/user';
import { useRouter } from 'vue-router'

const router = useRouter()
const userStore = useUserStore()
const showSidebar = ref(false)
const leaveData = ref([]);

const fetchLeaveHistory = async () => {
    try {
        const res = await http.get('/leaves');
        leaveData.value = res.data.data.slice(0, 5); // chỉ lấy 5 đơn gần nhất
    } catch (error) {
        console.log('Không lấy được lịch sử nghỉ phép:', error);
    }
};

const goProfile = () => {
    router.push('/employee/file')
}

const goToLeaves = () => {
    router.push('/employee/leaves')
}

const statusClass = (status) => {
    const value = (status || '').toLowerCase()

    if (value.includes('đã duyệt')) return 'status-approved'
    if (value.includes('từ chối')) return 'status-rejected'
    if (value.includes('chờ duyệt')) return 'status-pending'

    return 'status-default'
}

onMounted(async () => {
    try {
        const token = localStorage.getItem('token')

        if (!userStore.user) {
            const cachedUser = localStorage.getItem('user')
            if (cachedUser) {
                userStore.setUser(JSON.parse(cachedUser))
            }
        }

        if (!userStore.user && token) {
            const response = await http.get('/user')
            userStore.setUser(response.data)
            localStorage.setItem('user', JSON.stringify(response.data))
        }

        console.log('employee info:', userStore.user)
    } catch (error) {
        console.log('Không lấy được thông tin employee:', error?.response?.data || error)
    }
})

const formatStatus = (status) => {
    const value = (status || '').toLowerCase()

    if (value === 'active') return 'Đang làm việc'
    if (value === 'inactive') return 'Ngừng hoạt động'

    return status // fallback nếu có trạng thái khác
}

const employeeStatusClass = (status) => {
    const value = (status || '').toLowerCase()

    if (value === 'active') return 'status-active'
    if (value === 'inactive') return 'status-inactive'

    return 'status-default'
}

onMounted(() => {
    fetchLeaveHistory();
});
</script>

<style scoped>
.card {
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: none;
}

.card h2 {
    font-weight: 500;
}

.status-badge {
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.status-approved {
    color: #198754;
    font-weight: 600;
}

.status-rejected {
    color: #dc3545;
    font-weight: 600;
}

.status-pending {
    color: #fd7e14;
    font-weight: 600;
}

.status-default {
    color: #6c757d;
    font-weight: 600;
}

.status-active {
    background-color: #198754; /* xanh lá */
}

.status-inactive {
    background-color: #6c757d; /* xám */
}
</style>