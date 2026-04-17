<template>
    <div>
        <TheHeader />

        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false"
                class="d-lg-none">
                <SidebarAccountant />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAccountant />
                </div>

                <div class="col-12 col-lg-9">
                    <h1 class="mt-3">Dashboard kế toán</h1>

                    <div class="container-fluid">
                        <div class="row g-2 align-items-center mb-3">

                            <div class="col-auto">
                                <select v-model="month" class="form-select form-select-sm">
                                    <option v-for="m in 12" :key="m" :value="m">
                                        Tháng {{ m }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <select v-model="year" class="form-select form-select-sm">
                                    <option :value="2025">2025</option>
                                    <option :value="2026">2026</option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-primary btn-sm" @click="loadDashboard">
                                    Tìm kiếm
                                </button>
                            </div>

                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-money-bill-wave fs-1 text-primary"></i>

                                        <div>
                                            <p class="mb-1 text-muted">Tổng quỹ lương</p>
                                            <h3 class="mb-0">{{ data.total_payroll }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-users fs-1 text-success"></i>

                                        <div>
                                            <p class="mb-1 text-muted">Tổng nhân viên</p>
                                            <h3 class="mb-0">{{ data.total_employees }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- Status breakdown -->
                    <div class="container-fluid mt-4">
                        <div class="row g-3 mt-1">

                            <div class="col-6 col-md-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-pen fs-3 text-secondary"></i>
                                        <div>
                                            <p class="mb-1 text-muted">Bản nháp</p>
                                            <h4 class="mb-0">{{ data.status?.draft }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-calculator fs-3 text-primary"></i>
                                        <div>
                                            <p class="mb-1 text-muted">Đã tính toán</p>
                                            <h4 class="mb-0">{{ data.status?.calculated }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-circle-check fs-3 text-success"></i>
                                        <div>
                                            <p class="mb-1 text-muted">Đã được phê duyệt</p>
                                            <h4 class="mb-0">{{ data.status?.approved }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-bullhorn fs-3 text-warning"></i>
                                        <div>
                                            <p class="mb-1 text-muted">Đã công bố</p>
                                            <h4 class="mb-0">{{ data.status?.published }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card p-3 mb-4 mt-3">
                            <h5>Cảnh báo</h5>

                            <p class="text-danger">
                                Thiếu lương cơ bản: {{ data.warnings.missing_base_salary || 0 }}
                            </p>

                            <p class="text-warning">
                                Lương âm: {{ data.warnings.negative_salary || 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import TheHeader from '../../components/TheHeader.vue'
import SidebarAccountant from '../../components/SidebarAccountant.vue'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../services/http'

const showSidebar = ref(false)

const month = ref(4)
const year = ref(2026)

const data = ref({
    total_payroll: 0,
    total_employees: 0,
    calculated: 0,
    not_calculated: 0,
    warnings: {},
    status: {}
})

const loadDashboard = async () => {
    const res = await http.get('/accountant/dashboard', {
        params: {
            month: month.value,
            year: year.value
        }
    })

    data.value = res.data
}

onMounted(() => {
    loadDashboard()
})


</script>