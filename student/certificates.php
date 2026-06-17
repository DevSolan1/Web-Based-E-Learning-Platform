<?php
$pageTitle = 'My Certificates - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Certificate.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$certificate = new Certificate();
$certificates = $certificate->getStudentCertificates($_SESSION['user_id']);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">My Certificates</h2>

                <?php if (empty($certificates)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-award display-1 text-muted"></i>
                    <h4 class="mt-3">No certificates yet</h4>
                    <p class="text-muted">Complete a course to earn your certificate</p>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($certificates as $cert): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="certificate-badge mb-3">
                                    <i class="bi bi-award"></i>
                                </div>
                                <h5><?= htmlspecialchars($cert['course_title']) ?></h5>
                                <p class="text-muted mb-1">Certificate #<?= $cert['certificate_number'] ?></p>
                                <small class="text-muted">Issued: <?= date('M d, Y', strtotime($cert['issued_at'])) ?></small>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a href="certificate.php?id=<?= $cert['id'] ?>" class="btn btn-primary">View Certificate</a>
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

<?php require_once '../includes/footer.php'; ?>
