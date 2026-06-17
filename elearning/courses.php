<?php
$pageTitle = 'Browse Courses - E-Learning Platform';
require_once 'includes/header.php';
require_once 'classes/Course.php';

$course = new Course();
$categories = $course->getCategories();

$search = $_GET['search'] ?? '';
$categoryId = $_GET['category'] ?? null;

if ($search || $categoryId) {
    $courses = $course->search($search, $categoryId);
} else {
    $courses = $course->getAll('published');
}
?>

<main class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search courses...">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            <?php if ($search || $categoryId): ?>
                            <a href="courses.php" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Course Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        <?php if ($search): ?>
                            Search results for "<?= htmlspecialchars($search) ?>"
                        <?php else: ?>
                            All Courses
                        <?php endif; ?>
                        <small class="text-muted">(<?= count($courses) ?> courses)</small>
                    </h4>
                </div>
                
                <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">No courses found</h4>
                    <p class="text-muted">Try adjusting your search or filters</p>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($courses as $c): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card course-card h-100">
                            <img src="<?= $c['thumbnail'] ? 'uploads/thumbnails/' . $c['thumbnail'] : 'assets/img/course-default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($c['title']) ?>">
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($c['category_name'] ?? 'General') ?></span>
                                <h5 class="card-title"><?= htmlspecialchars($c['title']) ?></h5>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>
                                </p>
                                <div class="rating-stars mb-2">
                                    <?php 
                                    $rating = round($c['avg_rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1">(<?= number_format($c['avg_rating'] ?? 0, 1) ?>)</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-primary">
                                    <?= $c['price'] > 0 ? '$' . number_format($c['price'], 2) : 'Free' ?>
                                </span>
                                <a href="course.php?slug=<?= $c['slug'] ?>" class="btn btn-outline-primary btn-sm">View</a>
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

<?php require_once 'includes/footer.php'; ?>
