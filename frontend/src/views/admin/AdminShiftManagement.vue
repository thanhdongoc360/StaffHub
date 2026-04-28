<template>
  <div>
    <TheHeader />
    <div class="container-fluid mt-3">
      <a-button @click="showSidebar = true" class="d-lg-none mb-3">
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
          <div
            class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mt-3 gap-2">
            <h1 class="mb-0">Quản lý ca làm việc</h1>
            <a-button type="primary" @click="openCreate" class="d-block d-sm-inline-block">
              Thêm ca
            </a-button>
          </div>

          <div class="table-responsive mt-4">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Tên ca</th>
                  <th scope="col">Bắt đầu</th>
                  <th scope="col">Kết thúc</th>
                  <th scope="col">Hành động</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="shift in list" :key="shift.id">
                  <td>{{ shift.name }}</td>
                  <td>{{ shift.start_time }}</td>
                  <td>{{ shift.end_time }}</td>
                  <td>
                    <a-space>
                      <i class="fa-solid fa-pen-to-square action-edit" @click="openEdit(shift)"></i>
                      <i class="fa-solid fa-trash action-delete" @click="remove(shift.id)"></i>
                    </a-space>
                  </td>
                </tr>

                <tr v-if="list.length === 0">
                  <td colspan="4" class="text-center text-muted">Chưa có ca làm việc nào</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <a-modal :open="showModal" @update:open="showModal = $event" :title="isEdit ? 'Sửa ca' : 'Thêm ca'"
      ok-text="Lưu" cancel-text="Đóng" @ok="save" @cancel="showModal = false">
      <a-form layout="vertical">
        <a-form-item label="Tên ca">
          <input v-model="form.name" class="form-control" placeholder="VD: Ca sáng" />
        </a-form-item>

        <a-form-item label="Giờ bắt đầu">
          <input v-model="form.start_time" class="form-control" placeholder="08:00:00" />
        </a-form-item>

        <a-form-item label="Giờ kết thúc">
          <input v-model="form.end_time" class="form-control" placeholder="17:00:00" />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue'
import SidebarAdmin from '../../components/SidebarAdmin.vue'
import { ref, reactive, onMounted } from 'vue'
import http from '../../services/http'

const showSidebar = ref(false)
const list = ref([])
const showModal = ref(false)
const isEdit = ref(false)
const selectedId = ref(null)

const form = reactive({
  name: '',
  start_time: '',
  end_time: ''
})    

const resetForm = () => {
  form.name = ''
  form.start_time = ''
  form.end_time = ''
}

const fetchData = async () => {
  try {
    const res = await http.get('/admin/shifts')
    list.value = res.data
  } catch (error) {
    console.log(error)
  }
}

const openCreate = () => {
  isEdit.value = false
  selectedId.value = null
  resetForm()
  showModal.value = true
}

const openEdit = (item) => {
  isEdit.value = true
  selectedId.value = item.id
  form.name = item.name || ''
  form.start_time = item.start_time || ''
  form.end_time = item.end_time || ''
  showModal.value = true
}

const save = async () => {
  try {
    if (!form.name || !form.start_time || !form.end_time) {
      alert('Vui lòng nhập đủ thông tin ca làm việc')
      return
    }

    if (isEdit.value) {
      await http.put(`/admin/shifts/${selectedId.value}`, {
        name: form.name,
        start_time: form.start_time,
        end_time: form.end_time
      })
    } else {
      await http.post('/admin/shifts', {
        name: form.name,
        start_time: form.start_time,
        end_time: form.end_time
      })
    }

    showModal.value = false
    resetForm()
    await fetchData()
  } catch (error) {
    console.log(error)
  }
}

const remove = async (id) => {
  if (!confirm('Xóa ca này?')) return

  try {
    await http.delete(`/admin/shifts/${id}`)
    await fetchData()
  } catch (error) {
    console.log(error)
  }
}

onMounted(fetchData)
</script>

<style scoped>
.action-edit {
  color: #00bfff;
  cursor: pointer;
}

.action-delete {
  color: #dc3545;
  cursor: pointer;
}
</style>