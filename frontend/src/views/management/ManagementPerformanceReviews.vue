<template>
    <div>
        <TheHeader />

        <div class="container-fluid mt-3">
            <a-button @click="showSidebar = true" class="d-lg-none mb-3">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarManagement />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarManagement />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
                        <h1 class="mb-0">Hiệu suất phòng ban</h1>
                    </div>

                    <div class="mt-3 filter-panel">
                        <div class="d-flex flex-column flex-md-row flex-wrap gap-3">
                            <a-input class="filter-item" placeholder="Tìm tên / mã NV" v-model:value="search" @input="fetchData" />

                                <a-select v-model:value="month" class="filter-item-sm" @change="fetchData">
                                <a-select-option v-for="m in 12" :key="m" :value="m">
                                    Tháng {{ m }}
                                </a-select-option>
                            </a-select>

                                <a-input type="number" v-model:value="year" class="filter-item-sm" @change="fetchData" />

                            <a-button type="primary" @click="fetchData" class="d-block d-sm-inline-block">Tìm kiếm</a-button>
                        </div>
                    </div>

                    <div class="row mt-4 g-3">
                        <div class="col-12 col-sm-6 col-xl-3" v-for="card in kpiCards" :key="card.label">
                            <div class="p-3 border rounded text-center h-100">
                                <h5>{{ card.label }}</h5>
                                <h3>{{ card.value }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mã nhân viên</th>
                                    <th class="d-none d-lg-table-cell">Họ tên</th>
                                    <th class="d-none d-lg-table-cell">Chức vụ</th>
                                    <th>Trạng thái</th>
                                    <th class="d-none d-lg-table-cell">Điểm</th>
                                    <th>Xếp loại</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in list" :key="item.id">
                                    <td class="text-nowrap">{{ item.code }}</td>
                                    <td class="d-none d-lg-table-cell">{{ item.name }}</td>
                                    <td class="d-none d-md-table-cell">{{ item.position }}</td>
                                    <td>
                                        <span :class="statusClass(item.status)">
                                            {{ statusText(item.status) }}
                                        </span>
                                    </td>

                                    <td class="d-none d-lg-table-cell">{{ item.total_score ?? '-' }}</td>
                                    <td>{{ item.rank ?? '-' }}</td>

                                    <td>
                                        <a-button size="small" @click="openDetail(item)">
                                            Xem / Nhập
                                        </a-button>
                                    </td>
                                </tr>

                                <tr v-if="list.length === 0">
                                    <td colspan="7" class="text-center text-muted">
                                        Không có dữ liệu
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <a-modal v-model:open="showModal" title="Chi tiết đánh giá" width="800px" @cancel="showModal = false"
            :footer="null" @ok="saveReview">
            <div v-if="detail">
                <h3>{{ detail.employee.name }}</h3>
                <p>Mã NV: {{ detail.employee.code }}</p>
                <p>Chức vụ: {{ detail.employee.position }}</p>

                <hr />

                <a-form layout="vertical">
                    <a-form-item label="KPI Score">
                        <a-input-number v-model:value="detail.review.kpi_score" :min="0" :max="100" />
                    </a-form-item>

                    <a-form-item label="Discipline Score">
                        <a-input-number v-model:value="detail.review.discipline_score" :min="0" :max="100" />
                    </a-form-item>

                    <a-form-item label="Collaboration Score">
                        <a-input-number v-model:value="detail.review.collaboration_score" :min="0" :max="100" />
                    </a-form-item>

                    <a-form-item label="Growth Score">
                        <a-input-number v-model:value="detail.review.growth_score" :min="0" :max="100" />
                    </a-form-item>

                    <a-form-item label="Reviewer Comment">
                        <a-textarea v-model:value="detail.review.reviewer_comment" />
                    </a-form-item>
                </a-form>

                <div>
                    <b>Score:</b> {{ detail.review.total_score }} |
                    <b>Rank:</b> {{ detail.review.rank }}
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a-button class="d-block d-sm-inline-block" @click="showModal = false">
                        Đóng
                    </a-button>

                    <a-button v-if="detail.review.status === 'draft'" type="primary" class="d-block d-sm-inline-block" @click="submitReview">
                        Submit
                    </a-button>

                    <a-button v-if="detail.review.status === 'submitted'" type="primary" class="d-block d-sm-inline-block" @click="confirmReview">
                        Confirm
                    </a-button>
                </div>
            </div>


        </a-modal>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'
import http from '../../services/http'

const list = ref([])

const search = ref('')
const month = ref(new Date().getMonth() + 1)
const year = ref(new Date().getFullYear())

const showModal = ref(false)
const selectedEmployeeId = ref(null)

const showSidebar = ref(false)

const detail = ref(null)

const fetchData = async () => {
    try {
        const response = await http.get('/management/performance', {
            params: {
                month: month.value,
                year: year.value,
                search: search.value
            }
        })
        list.value = response.data
    } catch (error) {
        console.log('Fetch performance error: ', error)
    }
}

const kpiCards = computed(() => {
    const total = list.value.length

    const reviewed = list.value.filter(i => i.status === 'submitted' || i.status === 'confirmed').length
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

const statusClass = (status) => {
    switch (status) {
        case 'draft':
            return 'text-primary'
        case 'submitted':
            return 'text-warning'
        case 'confirmed':
            return 'text-success'
        default:
            return 'text-secondary'
    }
}

const statusText = (status) => {
    switch (status) {
        case 'draft':
            return 'Bản nháp'
        case 'submitted':
            return 'Đã gửi'
        case 'confirmed':
            return 'Đã duyệt'
        case 'not_reviewed':
            return 'Chưa đánh giá'
        default:
            return 'Chưa đánh giá'
    }
}

const openDetail = async (item) => {
    selectedEmployeeId.value = item.id

    try {
        const res = await http.get(`/management/performance/${item.id}`, {
            params: {
                month: month.value,
                year: year.value
            }
        })

        detail.value = res.data
        showModal.value = true
    } catch (error) {
        console.log('Detail error:', error)
    }
}

const saveReview = async () => {
    try {
        await http.post('/management/performance', {
            employee_id: detail.value.employee.id,
            month: month.value,
            year: year.value,

            kpi_score: detail.value.review.kpi_score,
            discipline_score: detail.value.review.discipline_score,
            collaboration_score: detail.value.review.collaboration_score,
            growth_score: detail.value.review.growth_score,

            kpi_comment: detail.value.review.kpi_comment,
            discipline_comment: detail.value.review.discipline_comment,
            collaboration_comment: detail.value.review.collaboration_comment,
            reviewer_comment: detail.value.review.reviewer_comment,
        })

        showModal.value = false
        await fetchData()

        detail.value = null
    } catch (error) {
        console.log('Save error:', error)
    }
}

const confirmReview = async () => {
    try {
        await http.put(`/management/performance/${detail.value.review.id}/confirm`)

        showModal.value = false
        await fetchData()

        detail.value = null
    } catch (error) {
        console.log('Confirm error:', error)
    }
}

const submitReview = async () => {
    try {
        await http.post('/management/performance', {
            ...detail.value.review,
            employee_id: detail.value.employee.id,
            month: month.value,
            year: year.value,
            status: 'submitted'
        })

        showModal.value = false
        await fetchData()

        detail.value = null
    } catch(error) {
        console.log('Submit error:', error)
    }
}

onMounted(() => {
    fetchData()
})

</script>