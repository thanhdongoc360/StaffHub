<template>
    <div>
        <TheHeader />
        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false" class="d-lg-none">
                <SidebarAccountant />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAccountant />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Đơn nghỉ phép của tôi</h1>
                        <a-button type="primary" @click="showModalLeave" class="d-block d-sm-inline-block">
                            Tạo đơn nghỉ phép
                        </a-button>
                    </div>

                    <div class="table-responsive mt-4">
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
                                <tr v-for="leave in leaves" :key="leave.id">
                                    <td>{{ leave.start_date }}</td>
                                    <td class="d-none d-md-table-cell">{{ leave.end_date }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ Math.ceil((new Date(leave.end_date) - new Date(leave.start_date)) / (1000 * 60 * 60 * 24)) + 1 }}
                                    </td>
                                    <td>{{ leave.type }}</td>
                                    <td class="d-none d-lg-table-cell">{{ leave.reason }}</td>
                                    <td>
                                        <span :class="statusClass(leave.status)">{{ leave.status }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ leave.created_at }}</td>
                                </tr>

                                <tr v-if="leaves.length === 0">
                                    <td colspan="7" class="text-center text-muted">
                                        Không có đơn nghỉ phép
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <a-modal v-model:open="isLeaveModalOpen" ok-text="Lưu" cancel-text="Đóng" @ok="createLeave"
            title="Tạo đơn nghỉ phép">
            <a-form layout="vertical">
                <a-form-item label="Khoảng ngày">
                    <a-range-picker v-model:value="dateRange" :placeholder="['Ngày bắt đầu', 'Ngày kết thúc']"
                        class="w-100" />
                </a-form-item>

                <a-form-item label="" name="status">
                    <a-select v-model:value="typeLeave" placeholder="Chọn loại nghỉ">
                        <a-select-option value="Nghỉ phép năm">Nghỉ phép năm</a-select-option>
                        <a-select-option value="Nghỉ ốm">Nghỉ ốm</a-select-option>
                        <a-select-option value="Nghỉ không lương">Nghỉ không lương</a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item label="Lý do">
                    <a-textarea v-model:value="reason" :rows="4" />
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAccountant from '../../components/SidebarAccountant.vue';
import { ref, onMounted } from 'vue'
import { useUserStore } from '../../stores/user';
import http from "../../services/http";

const isLeaveModalOpen = ref(false);
const showSidebar = ref(false)
const dateRange = ref([])
const typeLeave = ref('')
const reason = ref('')

const userStore = useUserStore()
const leaves = ref([])

const showModalLeave = () => {
    isLeaveModalOpen.value = true;
}

const statusClass = (status) => {
    const value = (status || '').toLowerCase()

    if (value.includes('đã duyệt')) return 'status-approved'
    if (value.includes('từ chối')) return 'status-rejected'
    if (value.includes('chờ duyệt')) return 'status-pending'

    return 'status-default'
}

const fetchLeaves = async () => {
    try {
        const res = await http.get('http://localhost:8000/api/accountant/leaves')

        leaves.value = res.data.data
    }
    catch(error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchLeaves()
})

const createLeave = async () => {
    const startDate = dateRange.value[0]?.format("YYYY-MM-DD")
    const endDate = dateRange.value[1]?.format("YYYY-MM-DD")

    try {
        const res = await http.post('accountant/leaves', {
            start_date: startDate,
            end_date: endDate,
            type: typeLeave.value,
            reason: reason.value
        })

        await fetchLeaves()

        isLeaveModalOpen.value = false

        dateRange.value = []
        typeLeave.value = ''
        reason.value = ''
    } catch (error) {
        console.log(error)
    }
}

</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
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
</style>