<?php
require_once __DIR__ . '/../config/config.php';

class Payment {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function create($studentId, $courseId, $amount, $paymentMethod = 'card') {
        $transactionId = $this->generateTransactionId();
        
        $stmt = $this->conn->prepare("INSERT INTO payments (student_id, course_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iidss", $studentId, $courseId, $amount, $paymentMethod, $transactionId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'payment_id' => $this->conn->insert_id, 'transaction_id' => $transactionId];
        }
        return ['success' => false, 'message' => 'Payment creation failed'];
    }
    
    private function generateTransactionId() {
        return 'TXN' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 8));
    }
    
    public function verify($paymentId, $transactionId) {
        // In production, integrate with actual payment gateway
        // This is a simulation for demo purposes
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE id = ? AND transaction_id = ?");
        $stmt->bind_param("is", $paymentId, $transactionId);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();
        
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found'];
        }
        
        // Mark payment as completed
        $stmt = $this->conn->prepare("UPDATE payments SET status = 'completed', paid_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();
        
        // Enroll student
        $enrollment = new Enrollment();
        $result = $enrollment->enroll($payment['student_id'], $payment['course_id']);
        
        if ($result['success']) {
            // Notify student
            $notification = new Notification();
            $course = new Course();
            $courseData = $course->getById($payment['course_id']);
            $notification->create($payment['student_id'], 'Payment Successful', "You have been enrolled in: {$courseData['title']}", 'payment');
            
            return ['success' => true, 'message' => 'Payment verified and enrollment completed'];
        }
        
        return $result;
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT p.*, c.title as course_title, u.first_name, u.last_name 
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                JOIN users u ON p.student_id = u.id 
                WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByStudent($studentId) {
        $stmt = $this->conn->prepare("SELECT p.*, c.title as course_title 
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                WHERE p.student_id = ? 
                ORDER BY p.created_at DESC");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getTeacherEarnings($teacherId) {
        $stmt = $this->conn->prepare("SELECT 
                SUM(CASE WHEN p.status = 'completed' THEN p.amount ELSE 0 END) as total_earnings,
                COUNT(CASE WHEN p.status = 'completed' THEN 1 END) as total_sales
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                WHERE c.teacher_id = ?");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getTeacherPayments($teacherId) {
        $stmt = $this->conn->prepare("SELECT p.*, c.title as course_title, u.first_name, u.last_name 
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                JOIN users u ON p.student_id = u.id 
                WHERE c.teacher_id = ? AND p.status = 'completed'
                ORDER BY p.paid_at DESC");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllPayments() {
        return $this->conn->query("SELECT p.*, c.title as course_title, u.first_name, u.last_name 
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                JOIN users u ON p.student_id = u.id 
                ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getTotalRevenue() {
        $result = $this->conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");
        return $result->fetch_assoc()['total'] ?? 0;
    }
}
?>
