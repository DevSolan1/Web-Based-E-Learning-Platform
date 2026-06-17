<?php
$pageTitle = 'Manage Users - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/User.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    redirect('../login.php');
}

$user = new User();
$role = $_GET['role'] ?? 'student';

if (isset($_GET['toggle'])) {
    $user->toggleStatus($_GET['toggle']);
    flash('success', 'User status updated');
    redirect('users.php?role=' . $role);
}

if (isset($_GET['delete'])) {
    $user->delete($_GET['delete']);
    flash('success', 'User deleted');
    redirect('users.php?role=' . $role);
}

$users = $user->getAllByRole($role);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Manage Users</h2>

                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $role === 'student' ? 'active' : '' ?>" href="?role=student">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $role === 'teacher' ? 'active' : '' ?>" href="?role=teacher">Teachers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $role === 'admin' ? 'active' : '' ?>" href="?role=admin">Admins</a>
                    </li>
                </ul>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No users found</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?= $u['profile_image'] ? 'uploads/profiles/' . $u['profile_image'] : 'assets/img/avatar.png' ?>" 
                                                     class="rounded-circle me-2" width="40" height="40" alt="">
                                                <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $u['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($u['role'] !== 'admin'): ?>
                                            <a href="?role=<?= $role ?>&toggle=<?= $u['id'] ?>" class="btn btn-sm btn-outline-<?= $u['is_active'] ? 'warning' : 'success' ?>">
                                                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </a>
                                            <a href="?role=<?= $role ?>&delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this user?">Delete</a>
                                            <?php endif; ?>
                                        </td>
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
