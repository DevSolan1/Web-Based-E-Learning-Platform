<?php
$pageTitle = 'My Students - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';
require_once '../classes/Enrollment.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$course = new Course();
$enrollment = new Enrollment();

$myCourses = $course->getByTeacher($_SESSION['user_id']);
$selectedCourse = $_GET['course'] ?? ($myCourses[0]['id'] ?? 0);

$students = $selectedCourse ? $enrollment->getCourseStudents($selectedCourse) : [];
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">My Students</h2>

                <?php if (empty($myCourses)): ?>
                <div class="alert alert-info">Create a course first to see enrolled students.</div>
                <?php else: ?>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Select Course</label>
                                <select name="course" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ($myCourses as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $selectedCourse == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['title']) ?> (<?= $c['enrollment_count'] ?> students)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Enrolled Students (<?= count($students) ?>)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Enrolled</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No students enrolled yet</td></tr>
                                <?php else: ?>
                                    <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?= $s['profile_image'] ? 'uploads/profiles/' . $s['profile_image'] : 'assets/img/avatar.png' ?>" 
                                                     class="rounded-circle me-2" width="40" height="40" alt="">
                                                <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($s['email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($s['enrolled_at'])) ?></td>
                                        <td>
                                            <div class="progress" style="width: 100px; height: 6px;">
                                                <div class="progress-bar" style="width: <?= $s['progress'] ?>%"></div>
                                            </div>
                                            <small><?= $s['progress'] ?>%</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $s['status'] === 'completed' ? 'success' : 'primary' ?>">
                                                <?= ucfirst($s['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
