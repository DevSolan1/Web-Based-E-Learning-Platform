<?php
$pageTitle = 'Checkout - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';
require_once '../classes/Payment.php';
require_once '../classes/Enrollment.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$courseId = $_GET['course'] ?? $_SESSION['pending_course'] ?? 0;
$course = new Course();
$courseData = $course->getById($courseId);

if (!$courseData) {
    flash('danger', 'Course not found');
    redirect('../courses.php');
}

$enrollment = new Enrollment();
if ($enrollment->isEnrolled($_SESSION['user_id'], $courseId)) {
    flash('info', 'You are already enrolled in this course');
    redirect('course.php?id=' . $courseId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment = new Payment();
    $result = $payment->create($_SESSION['user_id'], $courseId, $courseData['price'], $_POST['payment_method'] ?? 'card');
    
    if ($result['success']) {
        // Simulate payment verification (in production, integrate with payment gateway)
        $verifyResult = $payment->verify($result['payment_id'], $result['transaction_id']);
        
        if ($verifyResult['success']) {
            unset($_SESSION['pending_course']);
            flash('success', 'Payment successful! You are now enrolled.');
            redirect('course.php?id=' . $courseId);
        } else {
            flash('danger', $verifyResult['message']);
        }
    } else {
        flash('danger', $result['message']);
    }
}
?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Checkout</h2>

                <div class="row">
                    <div class="col-md-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Method</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_method" value="card" id="card" checked>
                                            <label class="form-check-label" for="card">
                                                <i class="bi bi-credit-card me-2"></i>Credit/Debit Card
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" value="paypal" id="paypal">
                                            <label class="form-check-label" for="paypal">
                                                <i class="bi bi-paypal me-2"></i>PayPal
                                            </label>
                                        </div>
                                    </div>

                                    <div id="cardDetails">
                                        <div class="mb-3">
                                            <label class="form-label">Card Number</label>
                                            <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" placeholder="MM/YY">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label">CVV</label>
                                                <input type="text" class="form-control" placeholder="123" maxlength="4">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Pay $<?= number_format($courseData['price'], 2) ?>
                                    </button>
                                </form>
                                <p class="text-muted text-center mt-3 small">
                                    <i class="bi bi-shield-check me-1"></i>Your payment is secure and encrypted
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex mb-3">
                                    <img src="../<?= $courseData['thumbnail'] ? 'uploads/thumbnails/' . $courseData['thumbnail'] : 'assets/img/course-default.jpg' ?>" 
                                         class="rounded me-3" width="80" alt="">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($courseData['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($courseData['first_name'] . ' ' . $courseData['last_name']) ?></small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Price</span>
                                    <span>$<?= number_format($courseData['price'], 2) ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total</strong>
                                    <strong class="text-primary">$<?= number_format($courseData['price'], 2) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
