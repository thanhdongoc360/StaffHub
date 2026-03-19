<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarAdmin />
                </div>

                <div class="col-9">
                    <div class="row">
                        <h1 class="mt-3">Hồ sơ cá nhân</h1>

                        <a-card title="Thông tin cơ bản" class="mb-4">
                            <a-form :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }">
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
                                    <a-button type="primary" @click="updateProfile" >
                                        Cập nhật
                                    </a-button>
                                </a-form-item>

                            </a-form>
                        </a-card>

                        <a-card title="Đổi mật khẩu">
                            <a-form :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }">
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
                                    <a-button type="primary" @click="changePassword">
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
import axios from 'axios'
import { message } from 'ant-design-vue'  

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
    const token = localStorage.getItem('token')

    const res = await axios.get(
        'http://localhost:8000/api/admin/profile',
        {
            headers: { Authorization: `Bearer ${token}` }
        }
    )

    profile.value = res.data.data
}

const updateProfile = async () => {
    try {
        const token = localStorage.getItem('token')

        const res = await axios.put(
            'http://localhost:8000/api/admin/profile',
            profile.value,
            {
                headers: { Authorization: `Bearer ${token}` }
            }
        )

        message.success(res.data.message)
    } catch(error) {
        message.error('Cập nhật thất bại')
    }
}

const changePassword = async () => {
    try {
        const token = localStorage.getItem('token')

        const res = await axios.put(
            'http://localhost:8000/api/admin/change-password',
            passwordForm.value,
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

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