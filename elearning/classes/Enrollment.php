<?php
require_once __DIR__ . '/../config/config.php';

class Enrollment {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function enroll($studentId, $courseId) {
        // Check if already enrolled
        if ($this->isEnrolled($studentId, $courseId)) {
            return ['success' => false, 'message' => 'Already enrolled in this course'];
        }
        
        $stmt = $this->conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $studentId, $courseId);
        
        if ($stmt->execute()) {
            // Send notification to teacher
            $course = new Course();
            $courseData = $course->getById($courseId);
            $notification = new Notification();
            $notification->create($courseData['teacher_id'], 'New Enrollment', "A new student enrolled in your course: {$courseData['title']}", 'enrollment');
            
            return ['success' => true, 'enrollment_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Enrollment failed'];
    }
    
    public function isEnrolled($studentId, $courseId) {
        $stmt = $this->conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public function getStudentCourses($studentId) {
        $stmt = $this->conn->prepare("SELECT e.*, c.title, c.slug, c.thumbnail, c.total_duration, u.first_name, u.last_name 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                JOIN users u ON c.teacher_id = u.id 
                WHERE e.student_id = ? 
                ORDER BY e.enrolled_at DESC");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCourseStudents($courseId) {
        $stmt = $this->conn->prepare("SELECT e.*, u.first_name, u.last_name, u.email, u.profile_image 
                FROM enrollments e 
                JOIN users u ON e.student_id = u.id 
                WHERE e.course_id = ? 
                ORDER BY e.enrolled_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateProgress($studentId, $courseId, $progress) {
        $stmt = $this->conn->prepare("UPDATE enrollments SET progress = ? WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("iii", $progress, $studentId, $courseId);
        return $stmt->execute();
    }
    
    public function markCompleted($studentId, $courseId) {
        $stmt = $this->conn->prepare("UPDATE enrollments SET status = 'completed', completed_at = NOW(), progress = 100 WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        return $stmt->execute();
    }
    
    public function getEnrollment($studentId, $courseId) {
        $stmt = $this->conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getTeacherTotalStudents($teacherId) {
        $stmt = $this->conn->prepare("SELECT COUNT(DISTINCT e.student_id) as total FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.teacher_id = ?");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
}
?>
