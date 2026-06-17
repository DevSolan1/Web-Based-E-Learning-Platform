<?php
$pageTitle = 'Teacher Dashboard - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';
require_once '../classes/Enrollment.php';
require_once '../classes/Payment.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    flash('danger', 'Access denied');
    redirect('../login.php');
}

$course = new Course();
$enrollment = new Enrollment();
$payment = new Payment();

$myCourses = $course->getByTeacher($_SESSION['user_id']);
$totalStudents = $enrollment->getTeacherTotalStudents($_SESSION['user_id']);
$earnings = $payment->getTeacherEarnings($_SESSION['user_id']);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Welcome, <?= $_SESSION['user_name'] ?>!</h2>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= count($myCourses) ?></h3>
                                    <small>Courses</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= $totalStudents ?></h3>
                                    <small>Students</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">$<?= number_format($earnings['total_earnings'] ?? 0, 0) ?></h3>
                                    <small>Earnings</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= $earnings['total_sales'] ?? 0 ?></h3>
                                    <small>Sales</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-body">
                        <a href="create-course.php" class="btn btn-primary me-2">
                            <i class="bi bi-plus-circle me-2"></i>Create New Course
                        </a>
                        <a href="my-courses.php" class="btn btn-outline-primary">
                            <i class="bi bi-list me-2"></i>Manage Courses
                        </a>
                    </div>
                </div>

                <!-- Recent Courses -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Courses</h5>
                        <a href="my-courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Students</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($myCourses)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No courses yet. Create your first course!</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($myCourses, 0, 5) as $c): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($c['title']) ?></strong>
                                            <br><small class="text-muted">$<?= number_format($c['price'], 2) ?></small>
                                        </td>
                                        <td><?= $c['enrollment_count'] ?></td>
                                        <td>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <?= number_format($c['avg_rating'] ?? 0, 1) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $c['status'] === 'published' ? 'success' : ($c['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                                                <?= ucfirst($c['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit-course.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
