<?php
require_once 'config/config.php';
require_once 'classes/Course.php';
require_once 'classes/Video.php';
require_once 'classes/Rating.php';
require_once 'classes/Enrollment.php';

$slug = $_GET['slug'] ?? '';
$course = new Course();
$courseData = $course->getBySlug($slug);

if (!$courseData) {
    flash('danger', 'Course not found');
    redirect('courses.php');
}

$pageTitle = $courseData['title'] . ' - E-Learning Platform';

require_once 'includes/header.php';

$video = new Video();
$videos = $video->getByCourse($courseData['id']);

$rating = new Rating();
$ratingStats = $rating->getCourseAverage($courseData['id']);
$reviews = $rating->getByCourse($courseData['id']);

$enrollment = new Enrollment();
$isEnrolled = isLoggedIn() ? $enrollment->isEnrolled($_SESSION['user_id'], $courseData['id']) : false;
$isOwner = isLoggedIn() && getUserRole() === 'teacher' && $courseData['teacher_id'] == $_SESSION['user_id'];

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if (!isLoggedIn()) {
        flash('warning', 'Please login to enroll in this course');
        redirect('login.php');
    }
    
    if ($courseData['price'] > 0) {
        $_SESSION['pending_course'] = $courseData['id'];
        redirect('student/checkout.php?course=' . $courseData['id']);
    } else {
        $result = $enrollment->enroll($_SESSION['user_id'], $courseData['id']);
        if ($result['success']) {
            flash('success', 'Successfully enrolled in the course!');
            redirect('student/course.php?id=' . $courseData['id']);
        } else {
            flash('danger', $result['message']);
        }
    }
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    if (!isLoggedIn() || !$isEnrolled) {
        flash('warning', 'You must be enrolled to rate this course');
    } else {
        $result = $rating->create($_SESSION['user_id'], $courseData['id'], $_POST['rating'], $_POST['review']);
        flash($result['success'] ? 'success' : 'danger', $result['message']);
    }
    redirect('course.php?slug=' . $slug);
}
?>

<main class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                        <li class="breadcrumb-item"><a href="courses.php?category=<?= $courseData['category_id'] ?>"><?= htmlspecialchars($courseData['category_name'] ?? 'General') ?></a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($courseData['title']) ?></li>
                    </ol>
                </nav>

                <h1 class="mb-3"><?= htmlspecialchars($courseData['title']) ?></h1>
                <p class="lead"><?= htmlspecialchars($courseData['short_description']) ?></p>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= round($ratingStats['avg_rating'] ?? 0) ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                        <span class="ms-1"><?= number_format($ratingStats['avg_rating'] ?? 0, 1) ?></span>
                        <span class="text-muted">(<?= $ratingStats['total_ratings'] ?? 0 ?> ratings)</span>
                    </div>
                    <span class="badge bg-<?= $courseData['level'] === 'beginner' ? 'success' : ($courseData['level'] === 'intermediate' ? 'warning' : 'danger') ?>">
                        <?= ucfirst($courseData['level']) ?>
                    </span>
                </div>

                <div class="mb-4">
                    <img src="<?= $courseData['thumbnail'] ? 'uploads/thumbnails/' . $courseData['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                         class="img-fluid rounded" alt="<?= htmlspecialchars($courseData['title']) ?>">
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">About This Course</h5></div>
                    <div class="card-body"><?= nl2br(htmlspecialchars($courseData['description'])) ?></div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Course Content</h5>
                        <span class="text-muted"><?= count($videos) ?> lectures • <?= floor($courseData['total_duration'] / 60) ?> min</span>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($videos)): ?>
                            <div class="list-group-item text-muted">No content available yet</div>
                        <?php else: ?>
                            <?php foreach ($videos as $index => $v): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-<?= $v['is_preview'] || $isEnrolled ? 'play-circle' : 'lock' ?> me-2"></i>
                                    <?= htmlspecialchars($v['title']) ?>
                                    <?php if ($v['is_preview']): ?><span class="badge bg-info ms-2">Preview</span><?php endif; ?>
                                </div>
                                <span class="text-muted"><?= floor($v['duration'] / 60) ?>:<?= str_pad($v['duration'] % 60, 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Instructor</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="<?= $courseData['teacher_image'] ? 'uploads/profiles/' . $courseData['teacher_image'] : 'assets/img/avatar.png' ?>" 
                                 class="rounded-circle me-3" width="80" height="80" alt="Instructor">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($courseData['first_name'] . ' ' . $courseData['last_name']) ?></h5>
                                <p class="text-muted mb-0">Course Instructor</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Student Reviews</h5></div>
                    <div class="card-body">
                        <?php if ($isEnrolled && !$rating->getStudentRating($_SESSION['user_id'], $courseData['id'])): ?>
                        <form method="POST" class="mb-4 p-3 bg-light rounded">
                            <h6>Leave a Review</h6>
                            <div class="mb-3">
                                <div class="rating-input">
                                    <input type="hidden" name="rating" value="5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star-fill star fs-4" style="cursor:pointer"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="review" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                            </div>
                            <button type="submit" name="submit_rating" class="btn btn-primary">Submit Review</button>
                        </form>
                        <?php endif; ?>

                        <?php if (empty($reviews)): ?>
                            <p class="text-muted">No reviews yet. Be the first to review!</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?= $review['profile_image'] ? 'uploads/profiles/' . $review['profile_image'] : 'assets/img/avatar.png' ?>" 
                                         class="rounded-circle me-2" width="40" height="40" alt="">
                                    <div>
                                        <strong><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></strong>
                                        <div class="rating-stars small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="display-5 fw-bold text-primary">
                                <?= $courseData['price'] > 0 ? '$' . number_format($courseData['price'], 2) : 'Free' ?>
                            </span>
                        </div>

                        <?php if ($isOwner): ?>
                            <a href="teacher/edit-course.php?id=<?= $courseData['id'] ?>" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-pencil me-2"></i>Edit Course
                            </a>
                        <?php elseif ($isEnrolled): ?>
                            <a href="student/course.php?id=<?= $courseData['id'] ?>" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-play-circle me-2"></i>Continue Learning
                            </a>
                        <?php else: ?>
                            <form method="POST">
                                <button type="submit" name="enroll" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-cart-plus me-2"></i><?= $courseData['price'] > 0 ? 'Buy Now' : 'Enroll Free' ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <hr>

                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-clock me-2"></i><?= floor($courseData['total_duration'] / 60) ?> minutes of content</li>
                            <li class="mb-2"><i class="bi bi-play-btn me-2"></i><?= count($videos) ?> lectures</li>
                            <li class="mb-2"><i class="bi bi-bar-chart me-2"></i><?= ucfirst($courseData['level']) ?> level</li>
                            <li class="mb-2"><i class="bi bi-infinity me-2"></i>Full lifetime access</li>
                            <li class="mb-2"><i class="bi bi-award me-2"></i>Certificate of completion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
