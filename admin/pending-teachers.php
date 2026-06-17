<?php
$pageTitle = 'Pending Teachers - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/User.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    flash('danger', 'Access denied');
    redirect('../login.php');
}

$user = new User();

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $user->approveTeacher($_POST['user_id']);
        flash('success', 'Teacher approved successfully');
    } elseif (isset($_POST['reject'])) {
        $user->rejectTeacher($_POST['user_id']);
        flash('success', 'Teacher application rejected');
    }
    redirect('pending-teachers.php');
}

$pendingTeachers = $user->getPendingTeachers();
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Pending Teacher Applications</h2>
                    <span class="badge bg-warning fs-6"><?= count($pendingTeachers) ?> pending</span>
                </div>

                <?php if (empty($pendingTeachers)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle display-1 text-success"></i>
                        <h4 class="mt-3">No Pending Applications</h4>
                        <p class="text-muted">All teacher applications have been reviewed.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($pendingTeachers as $teacher): ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-warning bg-opacity-25">
                                <span class="badge bg-warning">Pending Approval</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($teacher['email']) ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar me-2"></i>Applied: <?= date('M d, Y', strtotime($teacher['created_at'])) ?>
                                </p>
                                
                                <?php if ($teacher['cv_file']): ?>
                                <a href="../uploads/cv/<?= $teacher['cv_file'] ?>" target="_blank" class="btn btn-outline-primary btn-sm mb-3">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>View CV
                                </a>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white">
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="user_id" value="<?= $teacher['id'] ?>">
                                    <button type="submit" name="approve" class="btn btn-success flex-fill">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                    <button type="submit" name="reject" class="btn btn-danger flex-fill" onclick="return confirm('Are you sure you want to reject this application?')">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                </form>
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
