<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarAdmin />
                </div>
                <div class="col-9">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10">
                                <h1>Quản lý nhân viên</h1>
                            </div>
                            <div class="col-2 mt-3">
                                <div>
                                    <a-button type="primary" @click="showModal">Thêm nhân viên</a-button>
                                </div>
                            </div>
                        </div>

                    </div>


                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Họ tên</th>
                                <th scope="col">Vị trí</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Email</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user) in users" :key="user.id">
                                <td>{{ user.name }}</td>
                                <td>{{ user.employee?.position }}</td>
                                <td>{{ user.employee?.department }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.employee?.status }}</td>
                                <td>
                                    <a-space>
                                        <i class="fa-solid fa-eye" style="color: #00BFFF; cursor: pointer"
                                            @click="viewUser(user)"></i>
                                        <i class="fa-solid fa-pen-to-square" style="color: #00BFFF; cursor: pointer"
                                            @click="editUser(user)"></i>
                                        <i class="fa-solid fa-trash" style="color: red; cursor: pointer"
                                            @click="confirmDelete(user)"></i>
                                    </a-space>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a-modal ok-text="Đóng" :cancel-button-props="{ style: { display: 'none' } }" :open="isViewModalOpen"
        @update:open="(value) => isViewModalOpen = value" title="Chi tiết nhân viên">
        <p><b>Họ và tên:</b> {{ selectedUser?.name }}</p>
        <p><b>Email:</b> {{ selectedUser?.email }}</p>
        <p><b>Vai trò:</b> {{ selectedUser?.role }}</p>
        <p><b>Mã nhân viên:</b> {{ selectedUser?.employee?.employee_code }}</p>
        <p><b>Vị trí:</b> {{ selectedUser?.employee?.position }}</p>
        <p><b>Phòng ban:</b> {{ selectedUser?.employee?.department }}</p>
        <p><b>Trạng thái:</b> {{ selectedUser?.employee?.status }}</p>
    </a-modal>

    <a-modal v-model:open="isEditModalOpen" @update:open="(value) => isEditModalOpen = value" ok-text="Lưu"
        cancel-text="Đóng" @ok="updateUser" title="Chỉnh sửa nhân viên">
        <a-form layout="vertical">
            <a-form-item label="Họ và tên">
                <a-input :value="editForm.name" @update:value="(value) => editForm.name = value" />
            </a-form-item>
            <a-form-item label="Email">
                <a-input :value="editForm.email" @update:value="(value) => editForm.email = value" />
            </a-form-item>
            <a-form-item label="Vị trí">
                <a-input :value="editForm.position" @update:value="(value) => editForm.position = value" />
            </a-form-item>
            <a-form-item label="Phòng ban">
                <a-input :value="editForm.department" @update:value="(value) => editForm.department = value" />
            </a-form-item>
        </a-form>
    </a-modal>

    <a-modal v-model:open="isDeleteModalOpen" @update:open="(value) => isDeleteModalOpen = value" title="Xác nhận xóa"
        @ok="deleteUser" ok-text="Xóa" cancel-text="Hủy" ok-type="danger">
        <p>Bạn có chắc muốn xóa tài khoản này?</p>
        <p><b>{{ selectedUser?.name }}</b></p>
    </a-modal>

    <a-modal v-model:open="open" ok-text="Lưu" cancel-text="Đóng" @ok="handleOk">
        <h2>Thêm nhân viên</h2>
        <hr>

        <a-form ref="formRef" :model="addUser" :rules="rules" :label-col="labelCol" :wrapper-col="wrapperCol">
            <a-form-item label="Họ và tên" name="name">
                <a-input v-model:value="addUser.name" />
            </a-form-item>
            <a-form-item label="Email" name="email">
                <a-input v-model:value="addUser.email" />
            </a-form-item>
            <a-form-item label="Vị trí" name="position">
                <a-input v-model:value="addUser.position" />
            </a-form-item>
            <a-form-item label="Phòng ban" name="department">
                <a-input v-model:value="addUser.department" />
            </a-form-item>

            <a-form-item label="Mật khẩu" name="password"
                :rules="[{ required: true, message: 'Please input your password!' }]">
                <a-input-password v-model:value="addUser.password" />
            </a-form-item>

            <a-form-item label="Activity zone" name="status">
                <a-select v-model:value="addUser.status" placeholder="please select your zone">
                    <a-select-option value="Đang làm việc">Đang làm việc</a-select-option>
                    <a-select-option value="Nghỉ việc">Nghỉ việc</a-select-option>
                </a-select>
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarAdmin from '../../components/SidebarAdmin.vue';

import { ref, onMounted, reactive } from 'vue';
import http from "../../services/http";

const users = ref([])
const totalUsers = ref(0)

const open = ref(false);

const showModal = () => {
    open.value = true;
};

const fetchUsers = async () => {


    try {
        const response = await http.get('/admin/users')

        totalUsers.value = response.data.total;
        users.value = response.data.users;
    }
    catch (error) {
        console.log(error);
    }
}

onMounted(() => {
    fetchUsers()
})

const formRef = ref();
const labelCol = {
    span: 5,
};
const wrapperCol = {
    span: 13,
};
const initialAddUser = {
    name: '',
    email: '',
    position: '',
    department: '',
    password: '',
    status: 'Đang làm việc',
};

const addUser = reactive({ ...initialAddUser });

const resetForm = () => {
    Object.assign(addUser, initialAddUser);
    formRef.value?.clearValidate();
};
const rules = {
    name: [
        { required: true, message: 'Nhập họ tên', trigger: 'blur' },
    ],
    email: [
        { required: true, message: 'Email', trigger: 'blur' },
        { type: 'email', message: 'Email không hợp lệ', trigger: 'blur' },
    ],
    position: [
        { required: true, message: 'Nhập vị trí', trigger: 'blur' },
    ],
    department: [
        { required: true, message: 'Nhập phòng ban', trigger: 'blur' },
    ],
    password: [
        { required: true, message: 'Nhập mật khẩu', trigger: 'blur' },
    ],
    status: [
        { required: true, message: 'Chọn trạng thái', trigger: 'change' },
    ],
};

const handleOk = () => {
    formRef.value
        .validate()
        .then(async () => {
            try {
                await http.post('/admin/employees', addUser);

                await fetchUsers();
                open.value = false;
                resetForm();

            } catch (error) {
                console.log(error);
            }
        })
}

const isViewModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const selectedUser = ref(null)

const editForm = reactive({
    id: null,
    name: '',
    email: '',
    position: '',
    department: ''
})

const viewUser = (user) => {
    selectedUser.value = user
    isViewModalOpen.value = true
}

const editUser = (user) => {
    selectedUser.value = user
    editForm.id = user.id
    editForm.name = user.name || ''
    editForm.email = user.email || ''
    editForm.position = user.employee?.position || ''
    editForm.department = user.employee?.department || ''
    isEditModalOpen.value = true
}

const updateUser = async () => {
    try {
        await http.put(`/admin/users/${editForm.id}`, {
            name: editForm.name,
            email: editForm.email,
            position: editForm.position,
            department: editForm.department
        })

        isEditModalOpen.value = false
        await fetchUsers()
    } catch (error) {
        console.log(error)
    }
}

const confirmDelete = (user) => {
    selectedUser.value = user
    isDeleteModalOpen.value = true
}

const deleteUser = async () => {
    if (!selectedUser.value?.id) return

    try {
        await http.delete(`/admin/users/${selectedUser.value.id}`)

        isDeleteModalOpen.value = false
        await fetchUsers()
    } catch (error) {
        console.log(error)
    }
}
</script>