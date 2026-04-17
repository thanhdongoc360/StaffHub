<template>
    <div class="d-flex">

        <!-- Sidebar Accountant -->
        <div class="bg-light p-3 vh-100" style="width: 250px;">

            <ul class="nav nav-pills flex-column mb-auto">

                <!-- Dashboard -->
                <li class="nav-item">
                    <router-link to="/accountant/dashboard" class="nav-link text-black">
                        <i class="fa-solid fa-chart-pie me-2"></i>
                        Tổng quan lương
                    </router-link>
                </li>

                <!-- Salary List -->
                <li>
                    <router-link to="/accountant/salaries" class="nav-link text-black">
                        <i class="fa-solid fa-list me-2"></i>
                        Danh sách bảng lương
                    </router-link>
                </li>

                <li>
                    <router-link to="/accountant/leaves" class="nav-link text-black">
                        <i class="fa-solid fa-user me-2"></i>
                        Nghỉ phép
                    </router-link>
                </li>

                <!-- Profile -->
                <li>
                    <router-link to="/accountant/profile" class="nav-link text-black">
                        <i class="fa-solid fa-user me-2"></i>
                        Hồ sơ
                    </router-link>
                </li>

                <!-- Notification -->
                <li>
                    <router-link to="/accountant/notifications" class="nav-link text-black">
                        <i class="fa-solid fa-user me-2"></i>
                        Thông báo
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