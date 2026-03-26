<template>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 400px;">
      <h3 class="text-center mb-3" style="color: #1E90FF">StaffHub</h3>
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="inputEmail" class="form-label">Tên tài khoản</label>
          <input v-model="email" type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp">
          <div id="emailHelp" class="form-text"></div>
        </div>
        <div class="mb-3">
          <label for="inputPassword" class="form-label">Mật khẩu</label>
          <input v-model="password" type="password" class="form-control" id="inputPassword">
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="check1">
          <label class="form-check-label" for="check1">Ghi nhớ</label>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import http from "../services/http";
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'

const email = ref('')
const password = ref('')

const router = useRouter()
const userStore = useUserStore()

async function handleLogin() {
  try {
    const response = await http.post('/login', {
      email: email.value,
      password: password.value
    })

    const user = response.data.user
    const token = response.data.token

    userStore.setUser(user)
    userStore.setToken(token)

    localStorage.setItem('token', token)
    localStorage.setItem('user', JSON.stringify(user))

    if (response.data.role == 'admin') {
      router.push('/admin/dashboard')
    } 
    else {
      router.push('/employee/dashboard')
    }

  } catch (error) {
    console.log(error.response.data)
  }   
}
</script>