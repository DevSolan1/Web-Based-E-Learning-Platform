<?php
$pageTitle = 'Create Course - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$course = new Course();
$categories = $course->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'teacher_id' => $_SESSION['user_id'],
        'title' => $_POST['title'],
        'category_id' => $_POST['category_id'],
        'description' => $_POST['description'],
        'short_description' => $_POST['short_description'],
        'price' => $_POST['price'],
        'level' => $_POST['level'],
        'status' => $_POST['status'] ?? 'draft'
    ];

    // Handle thumbnail upload
    if (!empty($_FILES['thumbnail']['name'])) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../uploads/thumbnails/' . $filename);
        $data['thumbnail'] = $filename;
    }

    $result = $course->create($data);
    
    if ($result['success']) {
        flash('success', 'Course created successfully! Now add some videos.');
        redirect('manage-videos.php?course=' . $result['course_id']);
    } else {
        flash('danger', $result['message']);
    }
}
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Create New Course</h2>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Course Title *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Level</label>
                                    <select name="level" class="form-select">
                                        <option value="beginner">Beginner</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <input type="text" name="short_description" class="form-control" maxlength="500" placeholder="Brief overview (max 500 chars)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Detailed course description"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="0">
                                    <small class="text-muted">Set to 0 for free course</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft">Draft</option>
                                        <option value="published" selected>Published</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Course</button>
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
