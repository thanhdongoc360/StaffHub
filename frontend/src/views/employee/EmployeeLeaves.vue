<template>
    <div>
        <TheHeader />
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <SidebarEmployee />
                </div>

                <div class="col-9">
                    <div class="container-fluid">  
                        <div class="row">    
                            <div class="col-10">
                                <h1>Đơn nghỉ phép của tôi</h1>
                            </div>
                            <div class="col-2 mt-3">
                                <div>
                                    <a-button type="primary" @click="showModalLeave">Tạo đơn nghỉ phép</a-button>
                                </div>
                            </div>
                        </div>
                    </div>  

                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Ngày bắt đầu</th>
                                <th scope="col">Ngày kết thúc</th>
                                <th scope="col">Số ngày</th>
                                <th scope="col">Loại nghỉ</th>
                                <th scope="col">Lý do</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày nộp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="leave in leaves" :key="leave.id">
                                <td>{{ leave.start_date }}</td>
                                <td>{{ leave.end_date }}</td>
                                <td>
                                    {{ 
                                        Math.ceil(
                                            (new Date(leave.end_date) - new Date(leave.start_date)) / (1000*60*60*24)
                                        ) + 1
                                    }}
                                </td>
                                <td>{{ leave.type }}</td>
                                <td>{{ leave.reason }}</td>
                                <td>{{ leave.status }}</td>
                                <td>{{ leave.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a-modal v-model:open="isLeaveModalOpen" ok-text="Lưu" cancel-text="Đóng" @ok="createLeave"
            title="Tạo đơn nghỉ phép">
            <a-form layout="vertical">
                <a-form-item label="Khoảng ngày">
                    <a-range-picker v-model:value="dateRange" :placeholder="['Ngày bắt đầu', 'Ngày kết thúc']" />
                </a-form-item>

                <a-form-item label="" name="status">
                    <a-select v-model:value="typeLeave" placeholder="Chọn loại nghỉ">
                        <a-select-option value="Nghỉ phép năm">Nghỉ phép năm</a-select-option>
                        <a-select-option value="Nghỉ ốm">Nghỉ ốm</a-select-option>
                        <a-select-option value="Nghỉ không lương">Nghỉ không lương</a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item label="Lý do">
                    <a-textarea v-model:value="reason" :rows="4" />
                </a-form-item>
            </a-form>    
        </a-modal>
    </div>

</template>  

<script setup>
import TheHeader from '../../components/TheHeader.vue';
import SidebarEmployee from '../../components/SidebarEmployee.vue';
import { ref, onMounted } from 'vue'
import { useUserStore } from '../../stores/user';
import axios from 'axios';

const isLeaveModalOpen = ref(false);
const dateRange = ref([])
const typeLeave = ref('')
const reason = ref('')

const userStore = useUserStore()
const leaves = ref([])

const showModalLeave = () => {
    isLeaveModalOpen.value = true;
}

const fetchLeaves = async () => {
    const token = localStorage.getItem('token')

    try {
        const res = await axios.get('http://localhost:8000/api/leaves', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        leaves.value = res.data.data
    }
    catch(error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchLeaves()
})

const createLeave = async () => {
    const startDate = dateRange.value[0]?.format("YYYY-MM-DD")
    const endDate = dateRange.value[1]?.format("YYYY-MM-DD")

    const token = localStorage.getItem("token")

    try {
        const res = await axios.post('http://localhost:8000/api/leaves', {
            start_date: startDate,
            end_date: endDate,
            type: typeLeave.value,
            reason: reason.value
        },
        {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        await fetchLeaves()

        isLeaveModalOpen.value = false

        dateRange.value = []
        typeLeave.value = ''
        reason.value = ''
    } catch (error) {
        console.log(error)
    }
}

</script>