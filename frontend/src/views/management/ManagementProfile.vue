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
                    <h1 class="mb-4">Hồ sơ của tôi</h1>

                    <!-- Thông tin -->
                    <div class="card p-4 mb-4">
                        <h2 class="text-secondary mb-3">Thông tin cơ bản</h2>

                        <a-form layout="vertical">
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
                                <a-button type="primary" @click="updateProfile">
                                    Cập nhật
                                </a-button>
                            </a-form-item>
                        </a-form>
                    </div>

                    <!-- Đổi mật khẩu -->
                    <div class="card p-4">
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
                                <a-button type="primary" @click="handleChangePassword">
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
import TheHeader from '../../components/TheHeader.vue'
import SidebarManagement from '../../components/SidebarManagement.vue'
import { ref, onMounted } from 'vue'
import http from '../../services/http'

const showSidebar = ref(false)

const profile = ref({
    name: '',
    email: '',
    employee_code: '',
    position: '',
    department: '',
    phone: '',
    status: ''
})

const passwordForm = ref({
    current_password: '',
    new_password: '',
    confirm_password: ''
})

const fetchProfile = async () => {
    const res = await http.get('/management/profile')
    profile.value = res.data.data
}

onMounted(fetchProfile)

const updateProfile = async () => {
    await http.put('/management/profile', {
        email: profile.value.email,
        phone: profile.value.phone
    })

    alert('Cập nhật thành công')
}

const handleChangePassword = async () => {
    if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
        alert('Mật khẩu xác nhận không khớp')
        return
    }

    const res = await http.put('/management/change-password', {
        current_password: passwordForm.value.current_password,
        new_password: passwordForm.value.new_password
    })

    alert(res.data.message)

    passwordForm.value = {
        current_password: '',
        new_password: '',
        confirm_password: ''
    }
}
</script>

<style scoped>
.card {
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: none;
}
</style>