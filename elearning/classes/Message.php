<?php
require_once __DIR__ . '/../config/config.php';

class Message {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function send($senderId, $receiverId, $message, $subject = '', $courseId = null) {
        $subject = sanitize($subject);
        $message = sanitize($message);
        
        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, course_id, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $senderId, $receiverId, $courseId, $subject, $message);
        
        if ($stmt->execute()) {
            // Notify receiver
            $notification = new Notification();
            $notification->create($receiverId, 'New Message', "You have a new message", 'message');
            return ['success' => true, 'message_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to send message'];
    }
    
    public function getInbox($userId) {
        $stmt = $this->conn->prepare("SELECT m.*, u.first_name, u.last_name, u.profile_image, c.title as course_title 
                FROM messages m 
                JOIN users u ON m.sender_id = u.id 
                LEFT JOIN courses c ON m.course_id = c.id 
                WHERE m.receiver_id = ? 
                ORDER BY m.created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getSent($userId) {
        $stmt = $this->conn->prepare("SELECT m.*, u.first_name, u.last_name, u.profile_image, c.title as course_title 
                FROM messages m 
                JOIN users u ON m.receiver_id = u.id 
                LEFT JOIN courses c ON m.course_id = c.id 
                WHERE m.sender_id = ? 
                ORDER BY m.created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT m.*, 
                s.first_name as sender_first, s.last_name as sender_last,
                r.first_name as receiver_first, r.last_name as receiver_last,
                c.title as course_title 
                FROM messages m 
                JOIN users s ON m.sender_id = s.id 
                JOIN users r ON m.receiver_id = r.id 
                LEFT JOIN courses c ON m.course_id = c.id 
                WHERE m.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function markAsRead($id) {
        $stmt = $this->conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getUnreadCount($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
