<?php
require_once __DIR__ . '/../config/config.php';

class Notification {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function create($userId, $title, $message, $type = 'general') {
        $title = sanitize($title);
        $message = sanitize($message);
        
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $title, $message, $type);
        return $stmt->execute();
    }
    
    public function getByUser($userId, $limit = 20) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getUnreadCount($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    public function markAsRead($id) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function markAllAsRead($userId) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
