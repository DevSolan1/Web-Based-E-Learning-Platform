<?php
$pageTitle = 'My Courses - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$course = new Course();
$myCourses = $course->getByTeacher($_SESSION['user_id']);

if (isset($_GET['delete'])) {
    $course->delete($_GET['delete']);
    flash('success', 'Course deleted successfully');
    redirect('my-courses.php');
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
                    <h2 class="mb-0">My Courses</h2>
                    <a href="create-course.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Course
                    </a>
                </div>

                <?php if (empty($myCourses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">No courses yet</h4>
                    <p class="text-muted">Create your first course and start teaching</p>
                    <a href="create-course.php" class="btn btn-primary">Create Course</a>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Price</th>
                                    <th>Students</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myCourses as $c): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../<?= $c['thumbnail'] ? 'uploads/thumbnails/' . $c['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                                                 class="rounded me-2" width="60" height="40" style="object-fit:cover" alt="">
                                            <div>
                                                <strong><?= htmlspecialchars($c['title']) ?></strong>
                                                <br><small class="text-muted"><?= $c['category_name'] ?? 'Uncategorized' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($c['price'], 2) ?></td>
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
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit-course.php?id=<?= $c['id'] ?>" class="btn btn-outline-primary">Edit</a>
                                            <a href="manage-videos.php?course=<?= $c['id'] ?>" class="btn btn-outline-secondary">Videos</a>
                                            <a href="?delete=<?= $c['id'] ?>" class="btn btn-outline-danger" data-confirm="Delete this course?">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
