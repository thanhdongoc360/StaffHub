<template>
    <div class="d-flex">

        <!-- Sidebar -->
        <div class="bg-light p-3 vh-100" style="width: 250px;">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <router-link to="/management/dashboard" class="nav-link text-black">
                        <i class="fa-solid fa-gauge me-2"></i>Bảng điều khiển 
                    </router-link>
                </li>

                <li>
                    <router-link to="/management/employees" class="nav-link text-black">
                        <i class="fa-solid fa-users me-2"></i>Nhân viên phòng ban
                    </router-link>
                </li>
                <li>
                    <router-link to="/management/performance_reviews" class="nav-link text-black">
                        <i class="fa-solid fa-users me-2"></i>Hiệu suất phòng ban
                    </router-link>
                </li>
 
                <li>
                    <router-link to="/management/leaves" class="nav-link text-black">
                        <i class="fa-solid fa-calendar-check me-2"></i>Đơn nghỉ phép
                    </router-link>
                </li>

                <li>
                    <router-link to="/management/salary" class="nav-link text-black">
                        <i class="fa-solid fa-money-bill-wave me-2"></i>Lương
                    </router-link>
                </li>

                <li>
                    <router-link to="/management/profile" class="nav-link text-black">
                        <i class="fa-solid fa-user me-2"></i>Hồ sơ
                    </router-link>
                </li>

                <li>
                    <router-link to="/management/notifications" class="nav-link text-black">
                        <i class="fa-solid fa-bell me-2"></i>Thông báo
                    </router-link>
                </li>

                <li>
                    <router-link to="/management/profile" class="nav-link text-black">
                        <i class="fa-solid fa-bell me-2"></i>Hồ sơ
                    </router-link>
                </li>
            </ul>

            <hr>
        </div>

        <!-- Content -->
        <div class="flex-grow-1 p-4">
            <router-view />
        </div>

    </div>
</template>

<script>

</script>

<style>
.nav-link:hover {
  background-color: #0d6efd;
  color: #f8f9fa !important;
  border-radius: 6px;
  transition: 0.2s;
}

.router-link-exact-active {
    font-weight: bold;
    color: #0d6efd !important;
    background-color: #e7f1ff;
    border-radius: 6px;
}
</style>