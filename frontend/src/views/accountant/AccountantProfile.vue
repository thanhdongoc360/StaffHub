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
      <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false" class="d-lg-none">
        <SidebarAccountant />
      </a-drawer>

      <div class="row">
        <!-- Sidebar desktop -->
        <div class="d-none d-lg-block col-lg-3">
          <SidebarAccountant />
        </div>

        <!-- Nội dung chính -->
        <div class="col-12 col-lg-9">
          <h1 class="mb-4">Hồ sơ của tôi</h1>

          <!-- Thông tin cơ bản -->
          <div class="card p-3 p-lg-4 mb-4">
            <h2 class="text-secondary mb-3">Thông tin cơ bản</h2>

            <a-form layout="vertical" layout-lg="horizontal" :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
              <a-form-item label="Họ và tên">
                <a-input v-model:value="profile.name" disabled />
              </a-form-item>

              <a-form-item label="Email">
                <a-input v-model:value="profile.email" />
              </a-form-item>

              <a-form-item label="Số điện thoại">
                <a-input v-model:value="profile.phone" />
              </a-form-item>

              <a-form-item label="Vị trí">
                <a-input v-model:value="profile.position" disabled />
              </a-form-item>

              <a-form-item label="Phòng ban">
                <a-input v-model:value="profile.department" disabled />
              </a-form-item>

              <a-form-item>
                <a-button type="primary" @click="updateProfile" class="d-block d-sm-inline-block">
                  Cập nhật
                </a-button>
              </a-form-item>
            </a-form>
          </div>

          <!-- Đổi mật khẩu -->
          <div class="card p-3 p-lg-4">
            <h2 class="text-secondary mb-3">Đổi mật khẩu</h2>

            <a-form layout="vertical">
              <a-form-item label="Mật khẩu hiện tại">
                <a-input-password v-model:value="passwordForm.current_password" />
              </a-form-item>

              <a-form-item label="Mật khẩu mới">
                <a-input-password v-model:value="passwordForm.new_password" />
              </a-form-item>

              <a-form-item label="Xác nhận mật khẩu">
                <a-input-password v-model:value="passwordForm.confirm_password" />
              </a-form-item>

              <a-form-item>
                <a-button type="primary" @click="handleChangePassword" class="d-block d-sm-inline-block">
                  Đổi mật khẩu
                </a-button>
              </a-form-item>
            </a-form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAccountant from '../../components/SidebarAccountant.vue';
import { ref, onMounted } from 'vue';
import http from '../../services/http';

const showSidebar = ref(false);

const profile = ref({
  name: '',
  email: '',
  employee_code: '',
  position: '',
  department: '',
  phone: '',
  status: ''
});

const passwordForm = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
});

const fetchProfile = async () => {
  try {
    const res = await http.get('/accountant/profile');
    profile.value = res.data.data;
  } catch (error) {
    console.log('Error fetching profile: ', error.response);
  }
};  

onMounted(() => {
  fetchProfile();
});

const updateProfile = async () => {
  try {
    await http.put('/accountant/profile', {
      email: profile.value.email,
      phone: profile.value.phone
    });
    alert('Cập nhật thành công');
  } catch (error) {
    alert('Cập nhật thất bại');
  }
};

const handleChangePassword = async () => {
  try {
    if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
      alert('Mật khẩu xác nhận không khớp');
      return;
    }

    const res = await http.put('/accountant/change-password', {
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password
    });

    alert(res.data.message);

    passwordForm.value = {
      current_password: '',
      new_password: '',
      confirm_password: ''
    };
  } catch (error) {
    if (error.response) {
      alert(error.response.data.message);
    }
  }
};
</script>

<style scoped>
.card {
  background-color: #f8f9fa; /* nền nhẹ nhàng */
  border-radius: 12px;        /* bo góc */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* đổ bóng nhẹ */
  border: none;
}

.card h2 {
  font-weight: 500;
}

h1 {
  font-weight: 600;
}
</style>