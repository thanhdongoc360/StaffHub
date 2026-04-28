<template>
    <div>
        <TheHeader />
        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarEmployee />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarEmployee />
                </div>

                <div class="col-12 col-lg-9">
                    <h1 class="mb-0 mt-3">Lịch làm việc của tôi</h1>

                    <a-form class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center mt-3">
                        <a-select v-model:value="type" placeholder="Chọn phạm vi" class="w-100"
                            style="max-width: 220px;">
                            <a-select-option value="today">Hôm nay</a-select-option>
                            <a-select-option value="week">Tuần</a-select-option>
                            <a-select-option value="month">Tháng</a-select-option>
                        </a-select>

                        <a-button type="primary" @click="fetchData" class="d-block d-sm-inline-block">
                            Xem lịch
                        </a-button>
                    </a-form>

                    <!-- TABLE -->
                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Ca</th>
                                    <th class="d-none d-md-table-cell">Giờ</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in list" :key="item.id">
                                    <td>{{ item.work_date }}</td>
                                    <td>{{ item.shift?.name || '-' }}</td>
                                    <td class="d-none d-md-table-cell">
                                        {{ item.shift ? `${item.shift.start_time} - ${item.shift.end_time}` : '-' }}
                                    </td>
                                </tr>

                                <tr v-if="list.length === 0">
                                    <td colspan="3" class="text-center text-muted">Không có lịch</td>
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
import { ref, onMounted } from 'vue'
import http from "../../services/http";

const list = ref([])
const type = ref('week') // today | week | month
const showSidebar = ref(false)

const fetchData = async () => {
    const res = await http.get('/schedule/my', {
        params: { type: type.value }
    })

    list.value = res.data
}


onMounted(fetchData)
</script>