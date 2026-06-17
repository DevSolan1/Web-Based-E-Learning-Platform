<?php
require_once __DIR__ . '/../config/config.php';

class Video {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function create($data) {
        $courseId = (int)$data['course_id'];
        $title = sanitize($data['title']);
        $description = sanitize($data['description'] ?? '');
        $videoUrl = $data['video_url'];
        $duration = (int)($data['duration'] ?? 0);
        $isPreview = (int)($data['is_preview'] ?? 0);
        
        // Get next sort order
        $stmt = $this->conn->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 as next_order FROM videos WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $sortOrder = $stmt->get_result()->fetch_assoc()['next_order'];
        
        $stmt = $this->conn->prepare("INSERT INTO videos (course_id, title, description, video_url, duration, sort_order, is_preview) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiis", $courseId, $title, $description, $videoUrl, $duration, $sortOrder, $isPreview);
        
        if ($stmt->execute()) {
            // Update course duration
            $course = new Course();
            $course->updateDuration($courseId);
            return ['success' => true, 'video_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to add video'];
    }
    
    public function update($id, $data) {
        $title = sanitize($data['title']);
        $description = sanitize($data['description'] ?? '');
        $duration = (int)($data['duration'] ?? 0);
        $isPreview = (int)($data['is_preview'] ?? 0);
        
        $stmt = $this->conn->prepare("UPDATE videos SET title = ?, description = ?, duration = ?, is_preview = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $title, $description, $duration, $isPreview, $id);
        return $stmt->execute();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT v.*, c.title as course_title, c.teacher_id FROM videos v JOIN courses c ON v.course_id = c.id WHERE v.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByCourse($courseId) {
        $stmt = $this->conn->prepare("SELECT * FROM videos WHERE course_id = ? ORDER BY sort_order");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function delete($id) {
        $video = $this->getById($id);
        $stmt = $this->conn->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Update course duration
            $course = new Course();
            $course->updateDuration($video['course_id']);
            return true;
        }
        return false;
    }
    
    public function updateProgress($studentId, $videoId, $watchedDuration, $isCompleted = false) {
        $stmt = $this->conn->prepare("INSERT INTO video_progress (student_id, video_id, watched_duration, is_completed) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE watched_duration = ?, is_completed = ?");
        $completed = $isCompleted ? 1 : 0;
        $stmt->bind_param("iiiiii", $studentId, $videoId, $watchedDuration, $completed, $watchedDuration, $completed);
        return $stmt->execute();
    }
    
    public function getProgress($studentId, $videoId) {
        $stmt = $this->conn->prepare("SELECT * FROM video_progress WHERE student_id = ? AND video_id = ?");
        $stmt->bind_param("ii", $studentId, $videoId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getCourseProgress($studentId, $courseId) {
        $stmt = $this->conn->prepare("SELECT 
            (SELECT COUNT(*) FROM videos WHERE course_id = ?) as total_videos,
            (SELECT COUNT(*) FROM video_progress vp JOIN videos v ON vp.video_id = v.id WHERE vp.student_id = ? AND v.course_id = ? AND vp.is_completed = 1) as completed_videos");
        $stmt->bind_param("iii", $courseId, $studentId, $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function reorder($videoId, $newOrder) {
        $stmt = $this->conn->prepare("UPDATE videos SET sort_order = ? WHERE id = ?");
        $stmt->bind_param("ii", $newOrder, $videoId);
        return $stmt->execute();
    }
}
?>
