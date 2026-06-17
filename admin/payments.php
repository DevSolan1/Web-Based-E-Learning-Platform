<?php
$pageTitle = 'Payments - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Payment.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    redirect('../login.php');
}

$payment = new Payment();
$payments = $payment->getAllPayments();
$totalRevenue = $payment->getTotalRevenue();
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Payments</h2>

                <div class="card stat-card bg-success text-white mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">$<?= number_format($totalRevenue, 2) ?></h3>
                            <small>Total Revenue</small>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Transactions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No payments yet</td></tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $p): ?>
                                    <tr>
                                        <td><code><?= $p['transaction_id'] ?></code></td>
                                        <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                                        <td><?= htmlspecialchars($p['course_title']) ?></td>
                                        <td>$<?= number_format($p['amount'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $p['status'] === 'completed' ? 'success' : ($p['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($p['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y H:i', strtotime($p['created_at'])) ?></td>
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
