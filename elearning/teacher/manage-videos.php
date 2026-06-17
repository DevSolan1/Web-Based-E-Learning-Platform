<?php
$pageTitle = 'Manage Videos - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';
require_once '../classes/Video.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$courseId = $_GET['course'] ?? 0;
$course = new Course();
$courseData = $course->getById($courseId);

if (!$courseData || $courseData['teacher_id'] != $_SESSION['user_id']) {
    flash('danger', 'Course not found');
    redirect('my-courses.php');
}

$video = new Video();
$videos = $video->getByCourse($courseId);

// Handle video upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_video'])) {
        $videoUrl = '';
        if (!empty($_FILES['video_file']['name'])) {
            $ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
            $videoUrl = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['video_file']['tmp_name'], '../uploads/videos/' . $videoUrl);
        }

        $data = [
            'course_id' => $courseId,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'video_url' => $videoUrl,
            'duration' => $_POST['duration'] * 60,
            'is_preview' => isset($_POST['is_preview']) ? 1 : 0
        ];

        $result = $video->create($data);
        flash($result['success'] ? 'success' : 'danger', $result['success'] ? 'Video added successfully' : $result['message']);
    }
    redirect('manage-videos.php?course=' . $courseId);
}

// Handle delete
if (isset($_GET['delete'])) {
    $video->delete($_GET['delete']);
    flash('success', 'Video deleted');
    redirect('manage-videos.php?course=' . $courseId);
}
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Manage Videos</h2>
                        <small class="text-muted"><?= htmlspecialchars($courseData['title']) ?></small>
                    </div>
                    <a href="edit-course.php?id=<?= $courseId ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Course
                    </a>
                </div>

                <!-- Add Video Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Video</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Video Title *</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Duration (minutes)</label>
                                    <input type="number" name="duration" class="form-control" min="1" value="10">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_preview" class="form-check-input" id="isPreview">
                                        <label class="form-check-label" for="isPreview">Free Preview</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Video File *</label>
                                <input type="file" name="video_file" class="form-control" accept="video/*" required>
                            </div>
                            <button type="submit" name="add_video" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Video
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Video List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Videos (<?= count($videos) ?>)</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($videos)): ?>
                            <div class="list-group-item text-center text-muted py-4">No videos yet. Add your first video above.</div>
                        <?php else: ?>
                            <?php foreach ($videos as $index => $v): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-play-circle me-2"></i>
                                    <strong><?= ($index + 1) ?>. <?= htmlspecialchars($v['title']) ?></strong>
                                    <?php if ($v['is_preview']): ?>
                                        <span class="badge bg-info ms-2">Preview</span>
                                    <?php endif; ?>
                                    <br>
                                    <small class="text-muted">
                                        Duration: <?= floor($v['duration'] / 60) ?>:<?= str_pad($v['duration'] % 60, 2, '0', STR_PAD_LEFT) ?>
                                    </small>
                                </div>
                                <div>
                                    <a href="?course=<?= $courseId ?>&delete=<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this video?">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
