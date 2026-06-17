<?php
$pageTitle = 'Edit Course - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$courseId = $_GET['id'] ?? 0;
$course = new Course();
$courseData = $course->getById($courseId);

if (!$courseData || $courseData['teacher_id'] != $_SESSION['user_id']) {
    flash('danger', 'Course not found');
    redirect('my-courses.php');
}

$categories = $course->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'category_id' => $_POST['category_id'],
        'description' => $_POST['description'],
        'short_description' => $_POST['short_description'],
        'price' => $_POST['price'],
        'level' => $_POST['level'],
        'status' => $_POST['status']
    ];

    $course->update($courseId, $data);

    // Handle thumbnail upload
    if (!empty($_FILES['thumbnail']['name'])) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../uploads/thumbnails/' . $filename);
        $course->updateThumbnail($courseId, $filename);
    }

    flash('success', 'Course updated successfully');
    redirect('edit-course.php?id=' . $courseId);
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
                    <h2 class="mb-0">Edit Course</h2>
                    <a href="manage-videos.php?course=<?= $courseId ?>" class="btn btn-outline-primary">
                        <i class="bi bi-play-btn me-2"></i>Manage Videos
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Course Title *</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($courseData['title']) ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $courseData['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Level</label>
                                    <select name="level" class="form-select">
                                        <option value="beginner" <?= $courseData['level'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                        <option value="intermediate" <?= $courseData['level'] === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                        <option value="advanced" <?= $courseData['level'] === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" <?= $courseData['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="published" <?= $courseData['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                        <option value="archived" <?= $courseData['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <input type="text" name="short_description" class="form-control" maxlength="500" value="<?= htmlspecialchars($courseData['short_description']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($courseData['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= $courseData['price'] ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                    <?php if ($courseData['thumbnail']): ?>
                                    <small class="text-muted">Current: <?= $courseData['thumbnail'] ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Course</button>
                                <a href="my-courses.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
