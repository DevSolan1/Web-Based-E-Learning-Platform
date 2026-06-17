<div class="dashboard-sidebar p-3">
    <div class="text-center mb-3">
        <img src="../assets/img/avatar.png" class="rounded-circle mb-2" width="80" height="80" alt="">
        <h6 class="mb-0"><?= $_SESSION['user_name'] ?? 'Student' ?></h6>
        <small class="text-muted">Student</small>
    </div>
    <hr>
    <nav class="nav flex-column">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'my-courses.php' ? 'active' : '' ?>" href="my-courses.php">
            <i class="bi bi-book me-2"></i>My Courses
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'certificates.php' ? 'active' : '' ?>" href="certificates.php">
            <i class="bi bi-award me-2"></i>Certificates
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : '' ?>" href="messages.php">
            <i class="bi bi-envelope me-2"></i>Messages
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'notifications.php' ? 'active' : '' ?>" href="notifications.php">
            <i class="bi bi-bell me-2"></i>Notifications
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>" href="profile.php">
            <i class="bi bi-person me-2"></i>Profile
        </a>
        <hr>
        <a class="nav-link" href="../courses.php">
            <i class="bi bi-search me-2"></i>Browse Courses
        </a>
        <a class="nav-link text-danger" href="../logout.php">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </nav>
</div>
