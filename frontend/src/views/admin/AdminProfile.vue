<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <a-button @click="showSidebar = true" class="d-lg-none mb-2">
                <i class="fa-solid fa-bars"></i>
            </a-button>

            <a-drawer :visible="showSidebar" placement="left" width="260" @close="showSidebar = false" class="d-lg-none">
                <SidebarAdmin />
            </a-drawer>

            <div class="row">
                <div class="d-none d-lg-block col-lg-3">
                    <SidebarAdmin />
                </div>

                <div class="col-12 col-lg-9">
                    <div class="row mt-2 mt-lg-3">
                        <h1 class="mb-3">Hồ sơ cá nhân</h1>

                        <a-card title="Thông tin cơ bản" class="mb-4">
                            <a-form layout="vertical">
                                <a-form-item label="Họ và tên">
                                    <a-input v-model:value="profile.name" />
                                </a-form-item>
                                
                                <a-form-item label="Email">
                                    <a-input v-model:value="profile.email" />
                                </a-form-item>

                                <a-form-item label="Số điện thoại">
                                    <a-input v-model:value="profile.phone" />
                                </a-form-item>

                                <a-form-item>
                                    <a-button type="primary" @click="updateProfile" class="d-block d-sm-inline-block">
                                        Cập nhật
                                    </a-button>
                                </a-form-item>

                            </a-form>
                        </a-card>

                        <a-card title="Đổi mật khẩu">
                            <a-form layout="vertical">
                                <a-form-item label="Mật khẩu hiện tại">
                                    <a-input-password v-model:value="passwordForm.current_password" />
                                </a-form-item>
                                
                                <a-form-item label="Mật khẩu mởi">
                                    <a-input-password v-model:value="passwordForm.new_password" />
                                </a-form-item>
                                
                                <a-form-item label="Xác nhận mật khẩu">
                                    <a-input-password v-model:value="passwordForm.new_password_confirmation" />
                                </a-form-item>

                                <a-form-item>
                                    <a-button type="primary" @click="changePassword" class="d-block d-sm-inline-block">
                                        Đổi mật khẩu
                                    </a-button>
                                </a-form-item>
                            </a-form>
                        </a-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { ref, onMounted } from 'vue'
import http from "../../services/http";
import { message } from 'ant-design-vue'  

const showSidebar = ref(false)
const profile = ref({
    name: '',
    email: '',
    phone: ''
})

const passwordForm = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
})

const fetchProfile = async () => {
    const res = await http.get('/admin/profile')

    profile.value = res.data.data
}

const updateProfile = async () => {
    try {
        const res = await http.put('/admin/profile')

        message.success(res.data.message)
    } catch(error) {
        message.error('Cập nhật thất bại')
    }
}

const changePassword = async () => {
    try {
        const res = await http.put('/admin/change-password', passwordForm.value)

        message.success(res.data.message)

        passwordForm.value = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        }
    } catch (error) {
        message.error(error.response?.data?.message || 'Lỗi')
    }
}

onMounted(fetchProfile)

</script>