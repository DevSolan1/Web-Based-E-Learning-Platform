<?php
$pageTitle = 'Admin Dashboard - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/Course.php';
require_once '../classes/Payment.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    flash('danger', 'Access denied');
    redirect('../login.php');
}

$user = new User();
$course = new Course();
$payment = new Payment();

$userStats = $user->getStats();
$allCourses = $course->getAll();
$totalRevenue = $payment->getTotalRevenue();
$recentPayments = $payment->getAllPayments();
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Admin Dashboard</h2>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= ($userStats['student'] ?? 0) + ($userStats['teacher'] ?? 0) ?></h3>
                                    <small>Total Users</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= count($allCourses) ?></h3>
                                    <small>Courses</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= $userStats['teacher'] ?? 0 ?></h3>
                                    <small>Teachers</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-person-workspace"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">$<?= number_format($totalRevenue, 0) ?></h3>
                                    <small>Revenue</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Courses -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Courses</h5>
                                <a href="courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($allCourses, 0, 5) as $c): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($c['title']) ?></strong>
                                        <br><small class="text-muted"><?= $c['first_name'] . ' ' . $c['last_name'] ?></small>
                                    </div>
                                    <span class="badge bg-<?= $c['status'] === 'published' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($c['status']) ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Payments</h5>
                                <a href="payments.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php if (empty($recentPayments)): ?>
                                    <div class="list-group-item text-muted">No payments yet</div>
                                <?php else: ?>
                                    <?php foreach (array_slice($recentPayments, 0, 5) as $p): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></strong>
                                            <br><small class="text-muted"><?= $p['course_title'] ?></small>
                                        </div>
                                        <span class="text-success">$<?= number_format($p['amount'], 2) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
