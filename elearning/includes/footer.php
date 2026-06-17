<footer class="bg-dark text-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="bi bi-mortarboard-fill me-2"></i><?= SITE_NAME ?></h5>
                <p class="text-muted">Learn from the best instructors worldwide. Advance your career with our online courses.</p>
            </div>
            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= SITE_URL ?>/courses.php" class="text-muted">Browse Courses</a></li>
                    <li><a href="<?= SITE_URL ?>/register.php?role=teacher" class="text-muted">Become a Teacher</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact</h6>
                <p class="text-muted mb-1"><i class="bi bi-envelope me-2"></i>support@elearning.com</p>
                <p class="text-muted"><i class="bi bi-telephone me-2"></i>+1 234 567 890</p>
            </div>
        </div>
        <hr class="my-3">
        <p class="text-center text-muted mb-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
