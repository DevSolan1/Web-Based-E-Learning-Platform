<?php
$pageTitle = 'Notifications - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Notification.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$notification = new Notification();

if (isset($_GET['mark_read'])) {
    $notification->markAsRead($_GET['mark_read']);
    redirect('notifications.php');
}

if (isset($_GET['mark_all'])) {
    $notification->markAllAsRead($_SESSION['user_id']);
    flash('success', 'All notifications marked as read');
    redirect('notifications.php');
}

$notifications = $notification->getByUser($_SESSION['user_id']);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Notifications</h2>
                    <a href="?mark_all=1" class="btn btn-outline-primary btn-sm">Mark All as Read</a>
                </div>

                <div class="card">
                    <div class="list-group list-group-flush">
                        <?php if (empty($notifications)): ?>
                            <div class="list-group-item text-center text-muted py-5">No notifications</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                            <div class="list-group-item notification-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-<?= $notif['type'] === 'enrollment' ? 'person-plus' : 'bell' ?> me-2 text-primary"></i>
                                        <strong><?= htmlspecialchars($notif['title']) ?></strong>
                                        <p class="mb-0 mt-1"><?= htmlspecialchars($notif['message']) ?></p>
                                        <small class="text-muted"><?= date('M d, Y H:i', strtotime($notif['created_at'])) ?></small>
                                    </div>
                                    <?php if (!$notif['is_read']): ?>
                                    <a href="?mark_read=<?= $notif['id'] ?>" class="btn btn-sm btn-outline-secondary">Mark Read</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
