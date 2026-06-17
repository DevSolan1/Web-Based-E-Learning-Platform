<?php
$pageTitle = 'Issue Certificate - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';
require_once '../classes/Enrollment.php';
require_once '../classes/Certificate.php';
require_once '../classes/Video.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

$courseId = $_GET['course'] ?? 0;
$course = new Course();
$courseData = $course->getById($courseId);

if (!$courseData || $courseData['teacher_id'] != $_SESSION['user_id']) {
    flash('danger', 'Course not found');
    redirect('courses.php');
}

$enrollment = new Enrollment();
$certificate = new Certificate();
$video = new Video();

// Handle certificate issuance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_certificate'])) {
    $studentId = (int)$_POST['student_id'];
    
    // First mark the enrollment as completed
    $enrollment->markCompleted($studentId, $courseId);
    
    // Then generate certificate
    $result = $certificate->generate($studentId, $courseId);
    
    if ($result['success']) {
        flash('success', 'Certificate issued successfully');
    } else {
        flash('danger', $result['message']);
    }
    redirect('issue-certificate.php?course=' . $courseId);
}

$students = $enrollment->getCourseStudents($courseId);

// Get progress for each student
foreach ($students as &$student) {
    $progress = $video->getCourseProgress($student['student_id'], $courseId);
    $student['videos_completed'] = $progress['completed_videos'];
    $student['total_videos'] = $progress['total_videos'];
    if ($progress['total_videos'] > 0) {
        $student['progress_percent'] = round(($progress['completed_videos'] / $progress['total_videos']) * 100);
    } else {
        $student['progress_percent'] = 0;
    }
    $student['has_certificate'] = $certificate->getByStudentCourse($student['student_id'], $courseId) ? true : false;
}
unset($student);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Issue Certificates</h2>
                        <p class="text-muted mb-0"><?= htmlspecialchars($courseData['title']) ?></p>
                    </div>
                    <a href="students.php?course=<?= $courseId ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Students
                    </a>
                </div>

                <?php if (empty($students)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="mt-3">No Students Enrolled</h4>
                        <p class="text-muted">No students have enrolled in this course yet.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Enrolled Students</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($student['email']) ?></small>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 100px; height: 8px;">
                                            <div class="progress-bar bg-<?= $student['progress_percent'] == 100 ? 'success' : 'primary' ?>" 
                                                 style="width: <?= $student['progress_percent'] ?>%"></div>
                                        </div>
                                        <small><?= $student['videos_completed'] ?>/<?= $student['total_videos'] ?> videos (<?= $student['progress_percent'] ?>%)</small>
                                    </td>
                                    <td>
                                        <?php if ($student['has_certificate']): ?>
                                            <span class="badge bg-success"><i class="bi bi-award me-1"></i>Certified</span>
                                        <?php elseif ($student['status'] === 'completed'): ?>
                                            <span class="badge bg-info">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">In Progress</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$student['has_certificate']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">
                                            <button type="submit" name="issue_certificate" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Issue certificate to <?= htmlspecialchars($student['first_name']) ?>?')">
                                                <i class="bi bi-award me-1"></i>Issue Certificate
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="text-success"><i class="bi bi-check-circle"></i> Issued</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
