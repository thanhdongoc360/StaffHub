<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <!-- Sidebar drawer cho mobile -->
            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarEmployee />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarEmployee />
                </div>

                <div class="col-12 col-lg-9">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Chấm công</h1>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                        <button class="btn btn-success d-block d-sm-inline-block" @click="checkIn">
                            Check In
                        </button>

                        <button class="btn btn-danger d-block d-sm-inline-block" @click="checkOut">
                            Check Out
                        </button>
                    </div>

                    <div class="mt-3 filter-panel">
                        <div class="d-flex flex-column flex-sm-row flex-wrap gap-3 align-items-stretch align-items-sm-end">
                            <div class="filter-item filter-item-sm">
                                <label class="form-label fw-semibold mb-1">Tháng</label>
                                <input type="number" v-model="month" placeholder="Tháng" class="form-control w-100" />
                            </div>

                            <div class="filter-item filter-item-sm">
                                <label class="form-label fw-semibold mb-1">Năm</label>
                                <input type="number" v-model="year" placeholder="Năm" class="form-control w-100" />
                            </div>

                            <div class="filter-item filter-item-xs d-flex align-items-end">
                                <button class="btn btn-primary w-100" @click="fetchHistory">
                                    Xem lịch sử
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Check In</th>
                                    <th class="d-none d-md-table-cell">Check Out</th>
                                    <th>Trạng thái</th>
                                    <th class="d-none d-lg-table-cell">Giờ làm</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in list" :key="item.id">
                                    <td>{{ item.date }}</td>
                                    <td>{{ item.check_in_time ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ item.check_out_time ?? '-' }}</td>
                                    <td>
                                        <span :class="statusClass(item.status)">{{ statusText(item.status) }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ Math.floor((item.working_minutes || 0) / 60) }}h
                                        {{ (item.working_minutes || 0) % 60 }}m
                                    </td>
                                </tr>

                                <tr v-if="list.length === 0">
                                    <td colspan="5" class="text-center text-muted">
                                        Không có dữ liệu chấm công
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
import SidebarEmployee from '../../components/SidebarEmployee.vue';
import { ref, onMounted } from 'vue';
import http from '../../services/http';

const showSidebar = ref(false);

const list = ref([])
const month = ref(new Date().getMonth() + 1)
const year = ref(new Date().getFullYear())

const statusClass = (status) => {
    const value = (status || '').toLowerCase()

    if (value.includes('present') || value.includes('đi làm')) return 'status-present'
    if (value.includes('late') || value.includes('muộn')) return 'status-late'
    if (value.includes('half') || value.includes('nửa')) return 'status-half-day'

    return 'status-default'
}

const statusText = (status) => {
    const value = (status || '').toLowerCase()

    if (value.includes('present') || value.includes('đi làm')) return 'Đi làm'
    if (value.includes('late') || value.includes('muộn')) return 'Đi muộn'
    if (value.includes('half') || value.includes('nửa')) return 'Nửa ngày'

    return status || 'Không xác định'
}

const checkIn = async () => {
    try {
        await http.post('/attendance/check-in')
        alert('Check-in thành công')
        fetchHistory()
    } catch (e) {
        alert(e.response.data.message)
    }
}

const checkOut = async () => {
    try {
        await http.post('/attendance/check-out')
        alert('Check-out thành công')
        fetchHistory()
    } catch (e) {
        alert(e.response.data.message)
    }
}

const fetchHistory = async () => {
    const res = await http.get('/attendance/my', {
        params: {
            month: month.value,
            year: year.value
        }
    })

    list.value = res.data
}

onMounted(fetchHistory)

</script>

<style scoped>
.filter-item {
    flex: 1 1 240px;
    min-width: 0;
}

.filter-item-sm {
    flex: 0 1 180px;
}

.filter-item-xs {
    flex: 0 1 160px;
}

.status-present {
    color: #198754;
    font-weight: 600;
}

.status-late {
    color: #fd7e14;
    font-weight: 600;
}

.status-half-day {
    color: #0d6efd;
    font-weight: 600;
}

.status-default {
    color: #6c757d;
    font-weight: 600;
}

@media (max-width: 576px) {
    .filter-item,
    .filter-item-sm,
    .filter-item-xs {
        flex-basis: 100%;
    }
}
</style>