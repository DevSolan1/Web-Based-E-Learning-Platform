<?php
$pageTitle = 'Earnings - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Payment.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$payment = new Payment();
$earnings = $payment->getTeacherEarnings($_SESSION['user_id']);
$payments = $payment->getTeacherPayments($_SESSION['user_id']);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Earnings</h2>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">$<?= number_format($earnings['total_earnings'] ?? 0, 2) ?></h3>
                                    <small>Total Earnings</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= $earnings['total_sales'] ?? 0 ?></h3>
                                    <small>Total Sales</small>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Transactions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No transactions yet</td></tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $p): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($p['paid_at'])) ?></td>
                                        <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                                        <td><?= htmlspecialchars($p['course_title']) ?></td>
                                        <td class="text-success">+$<?= number_format($p['amount'], 2) ?></td>
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
