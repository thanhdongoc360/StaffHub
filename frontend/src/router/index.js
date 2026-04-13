// src/router/index.js
import { createRouter, createWebHistory } from "vue-router";

// Lấy user từ localStorage / sessionStorage
function getUser() {
  const user = localStorage.getItem("user");
  return user ? JSON.parse(user) : null;
}

const routes = [
  {
    path: "/",
    component: () => import("../views/Login.vue"),
  },
  {
    path: "/login",
    component: () => import("../views/Login.vue"),
  },

  // Admin routes
  {
    path: "/admin/dashboard",
    component: () => import("../views/admin/AdminDashboard.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/employees",
    component: () => import("../views/admin/AdminEmployees.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/leaves",
    component: () => import("../views/admin/AdminLeaveManagement.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/salary",
    component: () => import("../views/admin/AdminSalary.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/notification",
    component: () => import("../views/admin/AdminNotification.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/file",
    component: () => import("../views/admin/AdminProfile.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },

  // Employee routes
  {
    path: "/employee/dashboard",
    component: () => import("../views/employee/EmployeeDashboard.vue"),
    meta: { requiresAuth: true, role: "employee" },
  },
  {
    path: "/employee/leaves",
    component: () => import("../views/employee/EmployeeLeaves.vue"),
    meta: { requiresAuth: true, role: "employee" },
  },
  {
    path: "/employee/salary",
    component: () => import("../views/employee/MySalary.vue"),
    meta: { requiresAuth: true, role: "employee" },
  },
  {
    path: "/employee/notification",
    component: () => import("../views/employee/Notification.vue"),
    meta: { requiresAuth: true, role: "employee" },
  },
  {
    path: "/employee/file",
    component: () => import("../views/employee/EmployeeFile.vue"),
    meta: { requiresAuth: true, role: "employee" },
  },

  // Management routes
  {
    path: "/management/dashboard",
    component: () => import("../views/management/ManagementDashboard.vue"),
    meta: { requiresAuth: true, role: "management" },
  },
  {
    path: "/management/employees",
    component: () => import("../views/management/ManagementEmployees.vue"),
    meta: { requiresAuth: true, role: "management" },
  },
  {
    path: "/management/profile",
    component: () => import("../views/management/ManagementProfile.vue"),
    meta: { requiresAuth: true, role: "management" },
  },
  {
    path: "/management/leaves",
    component: () => import("../views/management/ManagementLeaves.vue"),
    meta: { requiresAuth: true, role: "management" },
  },
  {
    path: "/management/salary",
    component: () => import("../views/management/ManagementSalary.vue"),
    meta: { role: "management" },
  },
  {
    path: "/management/notifications",
    component: () => import("../views/management/ManagementNotifications.vue"),
    meta: { role: "management" },
  },
  {
    path: "/management/performance_reviews",
    component: () => import("../views/management/ManagementPerformanceReviews.vue"),
    meta: { role: "management" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Route guard
router.beforeEach((to, from, next) => {
  const user = getUser();

  // Nếu route cần login mà chưa có session
  if (to.meta.requiresAuth && !user) {
    return next({ path: "/login" });
  }

  // Nếu route có role nhưng user không đúng
  if (to.meta.role && user && user.role !== to.meta.role) {
    // Chuyển về dashboard tương ứng role
    const redirectPath =
      user.role === "admin" ? "/admin/dashboard" : "/employee/dashboard";
    return next({ path: redirectPath });
  }

  next();
});

export default router;
