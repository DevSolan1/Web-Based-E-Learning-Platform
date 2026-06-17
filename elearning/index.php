<?php
$pageTitle = 'Home - E-Learning Platform';
require_once 'includes/header.php';
require_once 'classes/Course.php';

$course = new Course();
$featuredCourses = $course->getFeatured(6);
$categories = $course->getCategories();
$latestCourses = $course->getAll('published', 8);
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Learn Without Limits</h1>
                    <p class="lead mb-4">Start, switch, or advance your career with thousands of courses from expert instructors.</p>
                    <form action="courses.php" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-lg" placeholder="What do you want to learn?">
                        <button type="submit" class="btn btn-light btn-lg px-4">Search</button>
                    </form>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <i class="bi bi-laptop display-1"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Browse Categories</h2>
            <div class="row g-3">
                <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="courses.php?category=<?= $cat['id'] ?>" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-folder2-open display-6 text-primary"></i>
                            <h6 class="mt-2 mb-0"><?= htmlspecialchars($cat['name']) ?></h6>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <?php if (!empty($featuredCourses)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">Featured Courses</h2>
            <div class="row g-4">
                <?php foreach ($featuredCourses as $c): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card course-card h-100">
                        <div class="position-relative">
                            <img src="<?= $c['thumbnail'] ? 'uploads/thumbnails/' . $c['thumbnail'] : 'assets/img/course-default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($c['title']) ?>">
                            <span class="badge bg-warning">Featured</span>
                        </div>
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
                            <span class="h5 mb-0 text-primary">$<?= number_format($c['price'], 2) ?></span>
                            <a href="course.php?slug=<?= $c['slug'] ?>" class="btn btn-outline-primary">View Course</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest Courses -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Latest Courses</h2>
                <a href="courses.php" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($latestCourses as $c): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card course-card h-100">
                        <img src="<?= $c['thumbnail'] ? 'uploads/thumbnails/' . $c['thumbnail'] : 'assets/img/course-default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($c['title']) ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($c['title']) ?></h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></p>
                            <div class="rating-stars small">
                                <?php 
                                $rating = round($c['avg_rating'] ?? 0);
                                for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <span class="fw-bold text-primary">$<?= number_format($c['price'], 2) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2>Become an Instructor</h2>
            <p class="lead mb-4">Share your knowledge and earn money by teaching online.</p>
            <a href="register.php?role=teacher" class="btn btn-light btn-lg">Start Teaching Today</a>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
