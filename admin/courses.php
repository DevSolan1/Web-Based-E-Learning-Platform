<?php
$pageTitle = 'Manage Courses - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    redirect('../login.php');
}

$course = new Course();

if (isset($_GET['delete'])) {
    $course->delete($_GET['delete']);
    flash('success', 'Course deleted');
    redirect('courses.php');
}

if (isset($_GET['feature'])) {
    $course->toggleFeatured($_GET['feature']);
    flash('success', 'Course featured status updated');
    redirect('courses.php');
}

$courses = $course->getAll();
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Manage Courses</h2>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Teacher</th>
                                    <th>Price</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($courses)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No courses found</td></tr>
                                <?php else: ?>
                                    <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?= $c['thumbnail'] ? 'uploads/thumbnails/' . $c['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                                                     class="rounded me-2" width="60" height="40" style="object-fit:cover" alt="">
                                                <div>
                                                    <strong><?= htmlspecialchars($c['title']) ?></strong>
                                                    <?php if ($c['is_featured']): ?>
                                                        <span class="badge bg-warning ms-1">Featured</span>
                                                    <?php endif; ?>
                                                    <br><small class="text-muted"><?= $c['category_name'] ?? 'Uncategorized' ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                                        <td>$<?= number_format($c['price'], 2) ?></td>
                                        <td><?= $c['enrollment_count'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $c['status'] === 'published' ? 'success' : ($c['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                                                <?= ucfirst($c['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?feature=<?= $c['id'] ?>" class="btn btn-outline-warning" title="Toggle Featured">
                                                    <i class="bi bi-star<?= $c['is_featured'] ? '-fill' : '' ?>"></i>
                                                </a>
                                                <a href="?delete=<?= $c['id'] ?>" class="btn btn-outline-danger" data-confirm="Delete this course?">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
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
