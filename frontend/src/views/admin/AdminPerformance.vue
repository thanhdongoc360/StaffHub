<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarAdmin />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAdmin />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="row mt-2 mt-lg-3">
                        <h1 class="mb-3">Hiệu suất toàn công ty</h1>

                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <a-input placeholder="Tìm tên / mã NV" v-model:value="search" />

                            <a-select v-model:value="month">
                                <a-select-option v-for="m in 12" :key="m" :value="m">
                                    Tháng {{ m }}
                                </a-select-option>
                            </a-select>

                            <a-input type="number" v-model:value="year" />

                            <a-button type="primary" @click="fetchData">
                                Tìm kiếm
                            </a-button>
                        </div>

                        <div class="row mt-4 g-3">
                            <div class="col-12 col-sm-6 col-xl-3" v-for="card in kpiCards" :key="card.label">
                                <div class="p-3 border rounded text-center h-100">
                                    <h5>{{ card.label }}</h5>
                                    <h3>{{ card.value }}</h3>
                                </div>
                            </div>
                        </div>

                        <!-- TABLE -->
                        <div class="table-responsive mt-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Chức vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Điểm</th>
                                        <th>Xếp loại</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="item in list" :key="item.id">
                                        <td>{{ item.code }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.position }}</td>
                                        <td>{{ statusText(item.status) }}</td>
                                        <td>{{ item.total_score ?? '-' }}</td>
                                        <td>{{ item.rank ?? '-' }}</td>

                                        <td>
                                            <a-button size="small" @click="openDetail(item)">
                                                Xem
                                            </a-button>
                                        </td>
                                    </tr>

                                    <tr v-if="list.length === 0">
                                        <td colspan="7" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <a-modal v-model:open="showModal" title="Chi tiết đánh giá" width="600px" :footer="null">
            <div v-if="detail">
                <h3>{{ detail.employee.name }}</h3>
                <p>Mã NV: {{ detail.employee.code }}</p>
                <p>Chức vụ: {{ detail.employee.position }}</p>

                <hr />

                <p>KPI: {{ detail.review?.kpi_score ?? '-' }}</p>
                <p>Discipline: {{ detail.review?.discipline_score ?? '-' }}</p>
                <p>Collaboration: {{ detail.review?.collaboration_score ?? '-' }}</p>
                <p>Growth: {{ detail.review?.growth_score ?? '-' }}</p>

                <p>Comment: {{ detail.review?.reviewer_comment ?? '-' }}</p>

                <hr />

                <b>Score:</b> {{ detail.review?.total_score ?? '-' }} |
                <b>Rank:</b> {{ detail.review?.rank ?? '-' }}
            </div>
        </a-modal>
    </div>
</template>


<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue';
import { ref, onMounted } from 'vue'
import http from "../../services/http";

const list = ref([])
const search = ref('')
const month = ref(new Date().getMonth() + 1)
const year = ref(new Date().getFullYear())

const showModal = ref(false)
const detail = ref(null)

const fetchData = async () => {
    const res = await http.get('/admin/performance', {
        params: {
            month: month.value,
            year: year.value,
            search: search.value
        }
    })

    list.value = res.data
}

import { computed } from 'vue'

const kpiCards = computed(() => {
    const total = list.value.length

    const reviewed = list.value.filter(
        i => i.status === 'submitted' || i.status === 'confirmed'
    ).length

    const notReviewed = total - reviewed

    const reviewedList = list.value.filter(
        i => i.status === 'submitted' || i.status === 'confirmed'
    )

    const avg = reviewedList.reduce(
        (sum, i) => sum + (i.total_score || 0),
        0
    ) / (reviewedList.length || 1)

    const aRate = reviewedList.filter(i => i.rank === 'A').length / (reviewedList.length || 1)

    return [
        { label: 'Đã đánh giá', value: reviewed },
        { label: 'Chưa đánh giá', value: notReviewed },
        { label: 'Điểm TB', value: avg.toFixed(1) },
        { label: 'Tỷ lệ A', value: (aRate * 100).toFixed(0) + '%' },
    ]
})

const openDetail = async (item) => {
    const res = await http.get(`/admin/performance/${item.id}`, {
        params: {
            month: month.value,
            year: year.value
        }
    })

    detail.value = res.data
    showModal.value = true
}

const statusText = (status) => {
    switch (status) {
        case 'draft': return 'Bản nháp'
        case 'submitted': return 'Đã gửi'
        case 'confirmed': return 'Đã duyệt'
        default: return 'Chưa đánh giá'
    }
}

onMounted(fetchData)
</script>