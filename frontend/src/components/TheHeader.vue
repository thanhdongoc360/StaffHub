<template>
    <nav class="navbar w-100" style="background-color: #1E90FF;">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold ">
                <span style="color: white;" @click="goHome">
                    <router-link to="/admin/dashboard" class="nav-link text-white">
                        StaffHub
                    </router-link>
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

const handleLogout = async () => {
    try {
        await axios.post(
            'http://localhost:8000/api/logout',
            {},
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            }
        )

        localStorage.removeItem('token')
        localStorage.removeItem('user')

        router.push('/login')
    }
    catch (error) {
        console.log(error)
    }
}


</script>