<?php
$pageTitle = 'My Courses - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Enrollment.php';
require_once '../classes/Video.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$enrollment = new Enrollment();
$video = new Video();
$myCourses = $enrollment->getStudentCourses($_SESSION['user_id']);

// Calculate actual progress for each course
foreach ($myCourses as &$course) {
    $progress = $video->getCourseProgress($_SESSION['user_id'], $course['course_id']);
    if ($progress['total_videos'] > 0) {
        $course['progress'] = round(($progress['completed_videos'] / $progress['total_videos']) * 100);
    } else {
        $course['progress'] = 0;
    }
}
unset($course);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">My Courses</h2>

                <?php if (empty($myCourses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">No courses yet</h4>
                    <p class="text-muted">Start learning by enrolling in a course</p>
                    <a href="../courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($myCourses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card course-card h-100">
                            <img src="../<?= $course['thumbnail'] ? 'uploads/thumbnails/' . $course['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                                 class="card-img-top" alt="">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                <p class="text-muted small"><?= htmlspecialchars($course['first_name'] . ' ' . $course['last_name']) ?></p>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $course['status'] === 'completed' ? 'success' : 'primary' ?>" 
                                         style="width: <?= $course['progress'] ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted"><?= $course['progress'] ?>% complete</small>
                                    <?php if ($course['status'] === 'completed'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="course.php?id=<?= $course['course_id'] ?>" class="btn btn-primary btn-sm w-100">
                                    <?= $course['status'] === 'completed' ? 'Review' : 'Continue' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
