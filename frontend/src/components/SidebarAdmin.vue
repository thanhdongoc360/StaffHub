<template>
    <div class="d-flex">

        <!-- Sidebar -->
        <div class="bg-light p-3 vh-100" style="width: 250px;">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <router-link to="/admin/dashboard" class="nav-link text-black">
                        <i class="fa-solid fa-gauge me-2"></i>Bảng điều khiển 
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/employees" class="nav-link text-black">
                        <i class="fa-solid fa-users me-2"></i>Nhân viên
                    </router-link>
                </li>
 
                <li>
                    <router-link to="/admin/leaves" class="nav-link text-black">
                        <i class="fa-solid fa-calendar-check me-2"></i>Đơn nghỉ phép
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/salary" class="nav-link text-black">
                        <i class="fa-solid fa-money-bill-wave me-2"></i>Lương
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/performance" class="nav-link text-black">
                        <i class="fa-solid fa-money-bill-wave me-2"></i>Hiệu suất nhân viên
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/shifts" class="nav-link text-black">
                        <i class="fa-solid fa-clock me-2"></i>Ca làm việc
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/file" class="nav-link text-black">
                        <i class="fa-solid fa-user me-2"></i>Hồ sơ
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/notification" class="nav-link text-black">
                        <i class="fa-solid fa-bell me-2"></i>Thông báo
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