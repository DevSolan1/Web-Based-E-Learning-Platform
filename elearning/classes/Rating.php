<?php
require_once __DIR__ . '/../config/config.php';

class Rating {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function create($studentId, $courseId, $rating, $review = '') {
        $review = sanitize($review);
        
        $stmt = $this->conn->prepare("INSERT INTO ratings (student_id, course_id, rating, review) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?, review = ?");
        $stmt->bind_param("iiisis", $studentId, $courseId, $rating, $review, $rating, $review);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Rating submitted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to submit rating'];
    }
    
    public function getByCourse($courseId) {
        $stmt = $this->conn->prepare("SELECT r.*, u.first_name, u.last_name, u.profile_image FROM ratings r JOIN users u ON r.student_id = u.id WHERE r.course_id = ? ORDER BY r.created_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCourseAverage($courseId) {
        $stmt = $this->conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getStudentRating($studentId, $courseId) {
        $stmt = $this->conn->prepare("SELECT * FROM ratings WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM ratings WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
