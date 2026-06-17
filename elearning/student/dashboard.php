<?php
$pageTitle = 'Student Dashboard - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Enrollment.php';
require_once '../classes/Certificate.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    flash('danger', 'Access denied');
    redirect('../login.php');
}

$enrollment = new Enrollment();
$certificate = new Certificate();

$myCourses = $enrollment->getStudentCourses($_SESSION['user_id']);
$myCertificates = $certificate->getStudentCertificates($_SESSION['user_id']);

$activeCourses = [];
$completedCourses = [];
foreach ($myCourses as $c) {
    if ($c['status'] === 'active') {
        $activeCourses[] = $c;
    } elseif ($c['status'] === 'completed') {
        $completedCourses[] = $c;
    }
}
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <h2 class="mb-4">Welcome back, <?= $_SESSION['user_name'] ?>!</h2>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= count($myCourses) ?></h3>
                                    <small>Enrolled Courses</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= count($completedCourses) ?></h3>
                                    <small>Completed</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= count($myCertificates) ?></h3>
                                    <small>Certificates</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-award"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue Learning -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Continue Learning</h5>
                        <a href="my-courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($activeCourses)): ?>
                            <p class="text-muted mb-0">No active courses. <a href="../courses.php">Browse courses</a></p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach (array_slice($activeCourses, 0, 3) as $course): ?>
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <img src="../<?= $course['thumbnail'] ? 'uploads/thumbnails/' . $course['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                                             class="card-img-top" alt="">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($course['title']) ?></h6>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar" style="width: <?= $course['progress'] ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $course['progress'] ?>% complete</small>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="course.php?id=<?= $course['course_id'] ?>" class="btn btn-primary btn-sm w-100">Continue</a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Certificates -->
                <?php if (!empty($myCertificates)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Certificates</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($myCertificates, 0, 3) as $cert): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-award text-warning me-2"></i>
                                <?= htmlspecialchars($cert['course_title']) ?>
                            </div>
                            <a href="certificate.php?id=<?= $cert['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
