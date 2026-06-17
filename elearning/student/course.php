<?php
require_once '../classes/Course.php';
require_once '../classes/Video.php';
require_once '../classes/Enrollment.php';
require_once '../classes/Certificate.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$courseId = $_GET['id'] ?? 0;
$course = new Course();
$courseData = $course->getById($courseId);

if (!$courseData) {
    flash('danger', 'Course not found');
    redirect('my-courses.php');
}

$enrollment = new Enrollment();
if (!$enrollment->isEnrolled($_SESSION['user_id'], $courseId)) {
    flash('danger', 'You are not enrolled in this course');
    redirect('my-courses.php');
}

$pageTitle = $courseData['title'] . ' - E-Learning Platform';
require_once '../includes/header.php';

$video = new Video();
$videos = $video->getByCourse($courseId);
$progress = $video->getCourseProgress($_SESSION['user_id'], $courseId);

$currentVideoId = $_GET['video'] ?? ($videos[0]['id'] ?? null);
$currentVideo = $currentVideoId ? $video->getById($currentVideoId) : null;

// Check completion
$enrollmentData = $enrollment->getEnrollment($_SESSION['user_id'], $courseId);
$isCompleted = $enrollmentData['status'] === 'completed';

// Handle course completion
if ($progress['total_videos'] > 0 && $progress['completed_videos'] >= $progress['total_videos'] && !$isCompleted) {
    $enrollment->markCompleted($_SESSION['user_id'], $courseId);
    $cert = new Certificate();
    $cert->generate($_SESSION['user_id'], $courseId);
    flash('success', 'Congratulations! You have completed this course!');
    redirect('course.php?id=' . $courseId);
}
?>

<main class="py-4">
    <div class="container-fluid">
        <div class="row">
            <!-- Video Player -->
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="my-courses.php">My Courses</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($courseData['title']) ?></li>
                    </ol>
                </nav>

                <?php if ($currentVideo): ?>
                <div class="video-container mb-3">
                    <video id="courseVideo" controls data-video-id="<?= $currentVideo['id'] ?>" data-course-id="<?= $courseId ?>">
                        <source src="../uploads/videos/<?= $currentVideo['video_url'] ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <h4><?= htmlspecialchars($currentVideo['title']) ?></h4>
                <p class="text-muted"><?= nl2br(htmlspecialchars($currentVideo['description'])) ?></p>
                <?php else: ?>
                <div class="alert alert-info">No videos available in this course yet.</div>
                <?php endif; ?>

                <!-- Progress Bar -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Course Progress</span>
                            <span><?= $progress['completed_videos'] ?>/<?= $progress['total_videos'] ?> lectures completed</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?= $progress['total_videos'] > 0 ? ($progress['completed_videos'] / $progress['total_videos'] * 100) : 0 ?>%"></div>
                        </div>
                        <?php if ($isCompleted): ?>
                        <div class="mt-3 text-center">
                            <span class="badge bg-success fs-6 p-2"><i class="bi bi-check-circle me-1"></i>Course Completed!</span>
                            <a href="certificate.php?course=<?= $courseId ?>" class="btn btn-warning ms-2">View Certificate</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Playlist Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Content</h5>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                        <?php foreach ($videos as $index => $v): 
                            $vProgress = $video->getProgress($_SESSION['user_id'], $v['id']);
                            $isComplete = $vProgress && $vProgress['is_completed'];
                        ?>
                        <a href="?id=<?= $courseId ?>&video=<?= $v['id'] ?>" 
                           class="list-group-item list-group-item-action playlist-item <?= $currentVideoId == $v['id'] ? 'active' : '' ?> <?= $isComplete ? 'completed' : '' ?>"
                           data-video-id="<?= $v['id'] ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-<?= $isComplete ? 'check-circle-fill text-success' : 'play-circle' ?> me-2"></i>
                                    <span><?= ($index + 1) ?>. <?= htmlspecialchars($v['title']) ?></span>
                                </div>
                                <small class="text-muted"><?= floor($v['duration'] / 60) ?>:<?= str_pad($v['duration'] % 60, 2, '0', STR_PAD_LEFT) ?></small>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
