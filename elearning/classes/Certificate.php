<?php
require_once __DIR__ . '/../config/config.php';

class Certificate {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function generate($studentId, $courseId) {
        // Check if certificate already exists
        $existing = $this->getByStudentCourse($studentId, $courseId);
        if ($existing) {
            return ['success' => true, 'certificate' => $existing];
        }
        
        // Check if course is completed
        $enrollment = new Enrollment();
        $enrollmentData = $enrollment->getEnrollment($studentId, $courseId);
        
        if (!$enrollmentData || $enrollmentData['status'] !== 'completed') {
            return ['success' => false, 'message' => 'Course not completed yet'];
        }
        
        $certNumber = $this->generateCertificateNumber();
        
        $stmt = $this->conn->prepare("INSERT INTO certificates (student_id, course_id, certificate_number) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $studentId, $courseId, $certNumber);
        
        if ($stmt->execute()) {
            $certificate = $this->getById($this->conn->insert_id);
            
            // Notify student
            $notification = new Notification();
            $notification->create($studentId, 'Certificate Generated', "Your certificate for the course is ready!", 'certificate');
            
            return ['success' => true, 'certificate' => $certificate];
        }
        return ['success' => false, 'message' => 'Failed to generate certificate'];
    }
    
    private function generateCertificateNumber() {
        return 'CERT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT cert.*, c.title as course_title, u.first_name, u.last_name, t.first_name as teacher_first, t.last_name as teacher_last 
                FROM certificates cert 
                JOIN courses c ON cert.course_id = c.id 
                JOIN users u ON cert.student_id = u.id 
                JOIN users t ON c.teacher_id = t.id 
                WHERE cert.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByNumber($certNumber) {
        $stmt = $this->conn->prepare("SELECT cert.*, c.title as course_title, u.first_name, u.last_name, t.first_name as teacher_first, t.last_name as teacher_last 
                FROM certificates cert 
                JOIN courses c ON cert.course_id = c.id 
                JOIN users u ON cert.student_id = u.id 
                JOIN users t ON c.teacher_id = t.id 
                WHERE cert.certificate_number = ?");
        $stmt->bind_param("s", $certNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByStudentCourse($studentId, $courseId) {
        $stmt = $this->conn->prepare("SELECT cert.*, c.title as course_title FROM certificates cert JOIN courses c ON cert.course_id = c.id WHERE cert.student_id = ? AND cert.course_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getStudentCertificates($studentId) {
        $stmt = $this->conn->prepare("SELECT cert.*, c.title as course_title, c.thumbnail FROM certificates cert JOIN courses c ON cert.course_id = c.id WHERE cert.student_id = ? ORDER BY cert.issued_at DESC");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
