<?php
$pageTitle = 'Login - E-Learning Platform';
require_once 'includes/header.php';
require_once 'classes/User.php';

if (isLoggedIn()) {
    redirect(getUserRole() . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->login($_POST['email'], $_POST['password']);
    
    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['first_name'];
        $_SESSION['user_role'] = $result['user']['role'];
        $_SESSION['user_email'] = $result['user']['email'];
        
        flash('success', 'Welcome back, ' . $result['user']['first_name'] . '!');
        redirect($result['user']['role'] . '/dashboard.php');
    } else {
        $error = $result['message'];
    }
}
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Welcome Back</h3>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>
                        
                        <p class="text-center mb-0">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Demo Admin: admin@elearning.com / admin123
                    </small>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
