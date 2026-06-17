<?php
$pageTitle = 'Compose Message - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Message.php';
require_once '../classes/Course.php';
require_once '../classes/Enrollment.php';

if (!isLoggedIn() || getUserRole() !== 'teacher') {
    redirect('../login.php');
}

// Get teacher's courses
$courseObj = new Course();
$myCourses = $courseObj->getByTeacher($_SESSION['user_id']);

// Get students if course is selected
$students = [];
if (isset($_GET['course_id'])) {
    $enrollmentObj = new Enrollment();
    $students = $enrollmentObj->getCourseStudents($_GET['course_id']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageObj = new Message();
    $result = $messageObj->send(
        $_SESSION['user_id'],
        $_POST['receiver_id'],
        $_POST['message'],
        $_POST['subject'],
        $_POST['course_id'] ?: null
    );
    
    if ($result['success']) {
        flash('success', 'Message sent successfully');
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

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Compose New Message</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($myCourses)): ?>
                            <div class="alert alert-info">
                                You need to create a course first to message students.
                                <a href="create-course.php" class="alert-link">Create Course</a>
                            </div>
                        <?php else: ?>
                            <form method="GET" class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Select Course</label>
                                    <select name="course_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">Choose a course...</option>
                                        <?php foreach ($myCourses as $course): ?>
                                            <option value="<?= $course['id'] ?>" <?= isset($_GET['course_id']) && $_GET['course_id'] == $course['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($course['title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <?php if (!empty($students)): ?>
                                <form method="POST">
                                    <input type="hidden" name="course_id" value="<?= $_GET['course_id'] ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Select Student</label>
                                        <select name="receiver_id" class="form-select" required>
                                            <option value="">Choose a student...</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?= $student['student_id'] ?>">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" placeholder="Message subject" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" class="form-control" rows="6" placeholder="Write your message..." required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Send Message
                                    </button>
                                </form>
                            <?php elseif (isset($_GET['course_id'])): ?>
                                <div class="alert alert-warning">
                                    No students enrolled in this course yet.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
