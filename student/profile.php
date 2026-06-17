<?php
$pageTitle = 'Profile - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/User.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

$user = new User();
$userData = $user->getById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $result = $user->updateProfile($_SESSION['user_id'], $_POST);
        if ($result) {
            $_SESSION['user_name'] = $_POST['first_name'];
            flash('success', 'Profile updated successfully');
        } else {
            flash('danger', 'Failed to update profile');
        }
    } elseif (isset($_POST['change_password'])) {
        $result = $user->changePassword($_SESSION['user_id'], $_POST['current_password'], $_POST['new_password']);
        flash($result['success'] ? 'success' : 'danger', $result['message']);
    }
    redirect('profile.php');
}
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">My Profile</h2>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($userData['first_name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($userData['last_name']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($userData['bio'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
