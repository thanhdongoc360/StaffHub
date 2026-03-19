import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/login',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/admin/dashboard',
        component: () => import('../views/admin/AdminDashboard.vue')
    },
    {
        path: '/admin/employees',
        component: () => import('../views/admin/AdminEmployees.vue')
    },
    {
        path: '/admin/leaves',
        component: () => import('../views/admin/AdminLeaveManagement.vue')
    }, 
    {
        path: '/admin/salary',
        component: () => import('../views/admin/AdminSalary.vue')
    },  
    {
        path: '/admin/notification',
        component: () => import('../views/admin/AdminNotification.vue')
    },  
    {
        path: '/admin/file',
        component: () => import('../views/admin/AdminProfile.vue')
    },  


    {
        path: '/employee/dashboard',
        component: () => import('../views/employee/EmployeeDashboard.vue')
    },  
    {
        path: '/employee/leaves',
        component: () => import('../views/employee/EmployeeLeaves.vue')
    },  
    
    {
        path: '/employee/salary',
        component: () => import('../views/employee/MySalary.vue')
    },  
    {
        path: '/employee/notification',
        component: () => import('../views/employee/Notification.vue')
    },  
    {
        path: '/employee/file',
        component: () => import('../views/employee/EmployeeFile.vue')
    },   
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
