<template>
    <nav class="navbar w-100" style="background-color: #1E90FF;">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold fs-4" style="cursor: pointer;">
                <span style="color: white;" @click="goHome">
                    StaffHub
                </span>
            </span>

            <button class="btn btn-outline-light" @click="handleLogout">
                Logout
            </button>
        </div>
    </nav>
</template>

<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
import http from "../services/http";

const goHome = () => {
    const user = JSON.parse(localStorage.getItem('user'))
    
    if (!user || !user.role) {
        // Nếu không có user, đi đến login
        router.push('/login')
        return
    }

    switch(user.role) {
        case 'admin':
            router.push('/admin/dashboard')
            break
        case 'employee':
            router.push('/employee/dashboard')
            break
        case 'management':
            router.push('/management/dashboard')
            break
        case 'accountant':
            router.push('/accountant/dashboard')
            break
        default:
            router.push('/login') // fallback
    }
}

const handleLogout = async () => {
    try {
        await http.post('/logout')

        localStorage.removeItem('token')
        localStorage.removeItem('user')

        router.push('/login')
    }
    catch (error) {
        console.log(error)
    }
}
</script>