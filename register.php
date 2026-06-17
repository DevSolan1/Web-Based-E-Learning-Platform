<?php
$pageTitle = 'Register - E-Learning Platform';
require_once 'includes/header.php';
require_once 'classes/User.php';

if (isLoggedIn()) {
    redirect(getUserRole() . '/dashboard.php');
}

$role = $_GET['role'] ?? 'student';
if (!in_array($role, ['student', 'teacher'])) {
    $role = 'student';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = 'Passwords do not match';
    } elseif (strlen($_POST['password']) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($_POST['role'] === 'teacher' && empty($_FILES['cv_file']['name'])) {
        $error = 'CV/Resume is required for teacher registration';
    } else {
        $cvFile = '';
        // Handle CV upload for teachers
        if ($_POST['role'] === 'teacher' && !empty($_FILES['cv_file']['name'])) {
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($_FILES['cv_file']['type'], $allowedTypes)) {
                $error = 'CV must be PDF or Word document';
            } else {
                $ext = pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION);
                $cvFile = uniqid('cv_') . '.' . $ext;
                $cvPath = 'uploads/cv/';
                if (!file_exists($cvPath)) {
                    mkdir($cvPath, 0777, true);
                }
                move_uploaded_file($_FILES['cv_file']['tmp_name'], $cvPath . $cvFile);
            }
        }
        
        if (!$error) {
            $user = new User();
            $result = $user->register([
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'role' => $_POST['role'],
                'cv_file' => $cvFile,
                'is_approved' => $_POST['role'] === 'teacher' ? 0 : 1
            ]);
            
            if ($result['success']) {
                if ($_POST['role'] === 'teacher') {
                    flash('success', 'Registration submitted! Please wait for admin approval before you can login.');
                } else {
                    flash('success', 'Registration successful! Please login.');
                }
                redirect('login.php');
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">
                            <?= $role === 'teacher' ? 'Become an Instructor' : 'Create Account' ?>
                        </h3>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <!-- Role Tabs -->
                        <ul class="nav nav-pills nav-justified mb-4">
                            <li class="nav-item">
                                <a class="nav-link <?= $role === 'student' ? 'active' : '' ?>" href="?role=student">
                                    <i class="bi bi-mortarboard me-1"></i>Student
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $role === 'teacher' ? 'active' : '' ?>" href="?role=teacher">
                                    <i class="bi bi-person-workspace me-1"></i>Teacher
                                </a>
                            </li>
                        </ul>
                        
                        <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            <input type="hidden" name="role" value="<?= $role ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <?php if ($role === 'teacher'): ?>
                            <div class="mb-3">
                                <label class="form-label">CV/Resume <span class="text-danger">*</span></label>
                                <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx" required>
                                <small class="text-muted">Upload your CV (PDF or Word document)</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Teacher accounts require admin approval. You will be notified once approved.
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the Terms of Service and Privacy Policy
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <?= $role === 'teacher' ? 'Submit Application' : 'Create Account' ?>
                            </button>
                        </form>
                        
                        <p class="text-center mb-0">
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
