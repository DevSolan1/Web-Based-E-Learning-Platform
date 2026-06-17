<div class="dashboard-sidebar p-3">
    <div class="text-center mb-3">
        <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:80px;height:80px">
            <i class="bi bi-shield-check fs-1"></i>
        </div>
        <h6 class="mb-0"><?= $_SESSION['user_name'] ?></h6>
        <small class="text-muted">Administrator</small>
    </div>
    <hr>
    <nav class="nav flex-column">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>" href="users.php">
            <i class="bi bi-people me-2"></i>Users
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'courses.php' ? 'active' : '' ?>" href="courses.php">
            <i class="bi bi-book me-2"></i>Courses
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>" href="categories.php">
            <i class="bi bi-folder me-2"></i>Categories
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'payments.php' ? 'active' : '' ?>" href="payments.php">
            <i class="bi bi-credit-card me-2"></i>Payments
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'notifications.php' ? 'active' : '' ?>" href="notifications.php">
            <i class="bi bi-bell me-2"></i>Notifications
        </a>
        <hr>
        <a class="nav-link text-danger" href="../logout.php">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </nav>
</div>
