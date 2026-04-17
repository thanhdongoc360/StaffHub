<template>
  <div>
    <TheHeader />

    <div class="container-fluid mt-3">
      <a-button @click="showSidebar = true" class="d-lg-none mb-3">
        <i class="fa-solid fa-bars"></i>
      </a-button>

      <a-drawer
        :visible="showSidebar"
        placement="left"
        width="260"
        @close="showSidebar = false"
        class="d-lg-none"
      >
        <SidebarManagement />
      </a-drawer>

      <div class="row">
        <div class="d-none d-lg-block col-lg-3">
          <SidebarManagement />
        </div>

        <div class="col-12 col-lg-9">
          <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
            <h1 class="mb-0">Quản lý đơn nghỉ phép</h1>
          </div>

          <div class="table-responsive mt-4">
            <table class="table">
              <thead>
                <tr>
                  <th>Tên nhân viên</th>
                  <th>Loại nghỉ</th>
                  <th class="d-none d-md-table-cell">Ngày bắt đầu</th>
                  <th class="d-none d-md-table-cell">Ngày kết thúc</th>
                  <th class="d-none d-lg-table-cell">Lý do</th>
                  <th>Trạng thái</th>
                  <th>Hành động</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="leave in leaves" :key="leave.id">
                  <td>{{ leave.employee?.user?.name }}</td>
                  <td>{{ leave.type }}</td>
                  <td class="d-none d-md-table-cell">{{ leave.start_date }}</td>
                  <td class="d-none d-md-table-cell">{{ leave.end_date }}</td>
                  <td class="d-none d-lg-table-cell">{{ leave.reason }}</td>
                  <td>{{ leave.status }}</td>

                  <td>
                    <template v-if="leave.status === 'Chờ duyệt'">
                      <div class="d-flex flex-column flex-sm-row gap-2">
                        <a-button
                          type="primary"
                          @click="approve(leave.id)"
                        >
                          Duyệt
                        </a-button>

                        <a-button
                          type="primary"
                          danger
                          @click="reject(leave.id)"
                        >
                          Từ chối
                        </a-button>
                      </div>
                    </template>

                    <template v-else-if="leave.status === 'Đã duyệt'">
                      <span class="text-success fw-bold">Đã duyệt</span>
                    </template>

                    <template v-else>
                      <span class="text-danger fw-bold">Từ chối</span>
                    </template>
                  </td>
                </tr>

                <tr v-if="leaves.length === 0">
                  <td colspan="7" class="text-center text-muted">
                    Không có đơn nghỉ phép nào
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
import { ref, onMounted } from 'vue'
import http from '../../services/http'
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'

const leaves = ref([])
const showSidebar = ref(false)

const fetchLeaves = async () => {
  try {
    const res = await http.get('/management/leaves')
    leaves.value = res.data.data || []
  } catch (error) {
    console.log(error)
  }
}

const approve = async (id) => {
  await http.post(`/management/leaves/${id}/approve`)
  fetchLeaves()
}

const reject = async (id) => {
  await http.post(`/management/leaves/${id}/reject`)
  fetchLeaves()
}

onMounted(fetchLeaves)
</script>

<style scoped>
.table td,
.table th {
  vertical-align: middle;
}
</style>