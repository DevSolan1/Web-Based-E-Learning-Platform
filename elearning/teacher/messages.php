<?php
$pageTitle = 'Messages - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Message.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$message = new Message();
$inbox = $message->getInbox($_SESSION['user_id']);
$sent = $message->getSent($_SESSION['user_id']);

$tab = $_GET['tab'] ?? 'inbox';
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Messages</h2>

                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'inbox' ? 'active' : '' ?>" href="?tab=inbox">Inbox (<?= count($inbox) ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'sent' ? 'active' : '' ?>" href="?tab=sent">Sent (<?= count($sent) ?>)</a>
                    </li>
                </ul>

                <div class="card">
                    <div class="list-group list-group-flush">
                        <?php 
                        $messages = $tab === 'inbox' ? $inbox : $sent;
                        if (empty($messages)): 
                        ?>
                            <div class="list-group-item text-center text-muted py-5">No messages</div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                            <a href="view-message.php?id=<?= $msg['id'] ?>" class="list-group-item list-group-item-action message-item <?= !$msg['is_read'] && $tab === 'inbox' ? 'unread' : '' ?>">
                                <div class="d-flex justify-content-between">
                                    <strong><?= htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) ?></strong>
                                    <small class="text-muted"><?= date('M d, Y', strtotime($msg['created_at'])) ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($msg['subject'] ?: 'No Subject') ?></p>
                                <small class="text-muted"><?= htmlspecialchars(substr($msg['message'], 0, 100)) ?>...</small>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
