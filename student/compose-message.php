<?php
$pageTitle = 'Compose Message - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Message.php';
require_once '../classes/Enrollment.php';
require_once '../classes/User.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    redirect('../login.php');
}

// Get enrolled courses and their teachers
$enrollment = new Enrollment();
$enrolledCourses = $enrollment->getStudentCourses($_SESSION['user_id']);

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
                        <?php if (empty($enrolledCourses)): ?>
                            <div class="alert alert-info">
                                You need to enroll in a course first to message teachers.
                                <a href="../courses.php" class="alert-link">Browse Courses</a>
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Select Course & Teacher</label>
                                    <select name="receiver_id" class="form-select" required onchange="updateCourseId(this)">
                                        <option value="">Choose a teacher...</option>
                                        <?php foreach ($enrolledCourses as $course): ?>
                                            <option value="<?= $course['teacher_id'] ?>" data-course-id="<?= $course['course_id'] ?>">
                                                <?= htmlspecialchars($course['teacher_name']) ?> - <?= htmlspecialchars($course['title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="course_id" id="course_id" value="">
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function updateCourseId(select) {
    const selectedOption = select.options[select.selectedIndex];
    const courseId = selectedOption.getAttribute('data-course-id');
    document.getElementById('course_id').value = courseId || '';
}
</script>

<?php require_once '../includes/footer.php'; ?>
