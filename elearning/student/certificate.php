<?php
$pageTitle = 'Certificate - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Certificate.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$certificate = new Certificate();

if (isset($_GET['id'])) {
    $cert = $certificate->getById($_GET['id']);
} elseif (isset($_GET['course'])) {
    $cert = $certificate->getByStudentCourse($_SESSION['user_id'], $_GET['course']);
}

if (!$cert || $cert['student_id'] != $_SESSION['user_id']) {
    flash('danger', 'Certificate not found');
    redirect('certificates.php');
}
?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-3">
                    <a href="certificates.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
                    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i>Print</button>
                </div>

                <div class="certificate" id="certificate">
                    <div class="certificate-badge">
                        <i class="bi bi-award"></i>
                    </div>
                    <h1 class="display-4 mb-3">Certificate of Completion</h1>
                    <p class="lead mb-4">This is to certify that</p>
                    <h2 class="display-5 text-primary mb-4"><?= htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']) ?></h2>
                    <p class="lead mb-4">has successfully completed the course</p>
                    <h3 class="mb-4"><?= htmlspecialchars($cert['course_title']) ?></h3>
                    <p class="text-muted mb-4">
                        Instructed by <?= htmlspecialchars($cert['teacher_first'] . ' ' . $cert['teacher_last']) ?>
                    </p>
                    <hr class="w-50 mx-auto">
                    <div class="row mt-4">
                        <div class="col-6">
                            <p class="mb-0"><strong>Certificate ID:</strong></p>
                            <p class="text-muted"><?= $cert['certificate_number'] ?></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-0"><strong>Issue Date:</strong></p>
                            <p class="text-muted"><?= date('F d, Y', strtotime($cert['issued_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .navbar, footer, .btn, .mb-3:first-child { display: none !important; }
    .certificate { border: 3px solid #0d6efd !important; }
}
</style>

<?php require_once '../includes/footer.php'; ?>
