<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function register($data) {
        $email = sanitize($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $firstName = sanitize($data['first_name']);
        $lastName = sanitize($data['last_name']);
        $role = sanitize($data['role'] ?? 'student');
        
        // Check if email exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $stmt = $this->conn->prepare("INSERT INTO users (email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $password, $firstName, $lastName, $role);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    public function login($email, $password) {
        $email = sanitize($email);
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $this->logLogin($user['id'], 'success');
                return ['success' => true, 'user' => $user];
            }
            $this->logLogin($user['id'], 'failed');
        }
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    private function logLogin($userId, $status) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $this->conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $ip, $userAgent, $status);
        $stmt->execute();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updateProfile($id, $data) {
        $firstName = sanitize($data['first_name']);
        $lastName = sanitize($data['last_name']);
        $phone = sanitize($data['phone'] ?? '');
        $bio = sanitize($data['bio'] ?? '');
        
        $stmt = $this->conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $phone, $bio, $id);
        return $stmt->execute();
    }
    
    public function updateProfileImage($id, $imagePath) {
        $stmt = $this->conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $imagePath, $id);
        return $stmt->execute();
    }
    
    public function changePassword($id, $currentPassword, $newPassword) {
        $user = $this->getById($id);
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        return ['success' => false, 'message' => 'Failed to change password'];
    }
    
    public function getAllByRole($role) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function toggleStatus($id) {
        $stmt = $this->conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getStats() {
        $stats = [];
        $result = $this->conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        while ($row = $result->fetch_assoc()) {
            $stats[$row['role']] = $row['count'];
        }
        return $stats;
    }
}
?>
