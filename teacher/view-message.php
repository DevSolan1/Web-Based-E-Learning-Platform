<?php
$pageTitle = 'View Message - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Message.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$messageObj = new Message();
$messageId = $_GET['id'] ?? 0;
$msg = $messageObj->getById($messageId);

if (!$msg || ($msg['receiver_id'] != $_SESSION['user_id'] && $msg['sender_id'] != $_SESSION['user_id'])) {
    flash('danger', 'Message not found');
    redirect('messages.php');
}

// Mark as read if receiver
if ($msg['receiver_id'] == $_SESSION['user_id'] && !$msg['is_read']) {
    $messageObj->markAsRead($messageId);
}

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $result = $messageObj->send(
        $_SESSION['user_id'],
        $msg['sender_id'],
        $_POST['message'],
        'Re: ' . $msg['subject'],
        $msg['course_id']
    );
    
    if ($result['success']) {
        flash('success', 'Reply sent successfully');
        redirect('messages.php');
    } else {
        flash('danger', $result['message']);
    }
}
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <a href="messages.php" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left me-2"></i>Back to Messages
                </a>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?= htmlspecialchars($msg['subject'] ?: 'No Subject') ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <strong>From:</strong> <?= htmlspecialchars($msg['sender_first'] . ' ' . $msg['sender_last']) ?>
                                <br>
                                <strong>To:</strong> <?= htmlspecialchars($msg['receiver_first'] . ' ' . $msg['receiver_last']) ?>
                            </div>
                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></small>
                        </div>
                        <hr>
                        <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    </div>
                </div>

                <?php if ($msg['sender_id'] != $_SESSION['user_id']): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reply</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
                            </div>
                            <button type="submit" name="reply" class="btn btn-primary">Send Reply</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
