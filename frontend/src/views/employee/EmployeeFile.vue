<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarEmployee />
                </div>
                <div class="col-9">
                    <h1>Hồ sơ của tôi</h1>

                    <div class="border border-secondary">
                        <h2 style="color: gray;" class="mt-5">Thông tin cơ bản</h2>

                        <a-form layout="horizontal" :label-col="{ span: 2 }" :wrapper-col="{ span: 18 }">
                            <a-form-item label="Họ và tên">
                                <a-input v-model:value="profile.name" disabled></a-input>
                            </a-form-item>

                            <a-form-item label="Email">
                                <a-input v-model:value="profile.email"></a-input>
                            </a-form-item>

                            <a-form-item label="Số điện thoại">
                                <a-input v-model:value="profile.phone"></a-input>
                            </a-form-item>

                            <a-form-item label="Vị trí">
                                <a-input v-model:value="profile.position" disabled></a-input>
                            </a-form-item>

                            <a-form-item label="Phòng ban">
                                <a-input v-model:value="profile.department" disabled></a-input>
                            </a-form-item>

                            <a-form-item @click="updateProfile">
                                <a-button type="primary">Submit</a-button>
                            </a-form-item>
                        </a-form>
                    </div>

                    <div class="border border-secondary mt-5">
                        <h2 style="color: gray;">Đổi mật khẩu</h2>

                        <a-form layout="horizontal" :label-col="{ span: 4 }" :wrapper-col="{ span: 16 }">
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
import TheHeader from '../../components/TheHeader.vue';
import SidebarEmployee from '../../components/SidebarEmployee.vue';

import { ref, onMounted } from 'vue'
import axios from 'axios'

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
    try {
        const token = localStorage.getItem('token')

        const res = await axios.get(
            'http://localhost:8000/api/employee/profile',
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        console.log('Response:', res.data)

        profile.value = res.data.data
    } catch (error) {
        console.log('Error: ', error.response)
    }
}

onMounted(() => {
    fetchProfile()
})

const updateProfile = async () => {
    const token = localStorage.getItem('token')

    await axios.put(
        'http://localhost:8000/api/employee/profile',
        {
            email: profile.value.email,
            phone: profile.value.phone
        },
        {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }
    )

    alert('Cập nhật thành công')
}

const handleChangePassword = async () => {
    try {
        if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
            alert('Mật khẩu xác nhận không khớp')
            return
        }

        const token = localStorage.getItem('token')

        const res = await axios.put(
            'http://localhost:8000/api/employee/change-password',
            {
                current_password: passwordForm.value.current_password,
                new_password: passwordForm.value.new_password
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        )

        alert(res.data.message)

        passwordForm.value = {
            current_password: '',
            new_password: '',
            confirm_password: ''
        }
    } catch (error) {
        if (error.response) {
            alert(error.response.data.message)
        }
    }
}

</script>