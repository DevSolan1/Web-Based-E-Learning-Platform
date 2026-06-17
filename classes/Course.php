<?php
require_once __DIR__ . '/../config/config.php';

class Course {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    public function create($data) {
        $teacherId = (int)$data['teacher_id'];
        $categoryId = (int)($data['category_id'] ?? 0);
        $title = sanitize($data['title']);
        $slug = $this->createSlug($title);
        $description = sanitize($data['description'] ?? '');
        $shortDesc = sanitize($data['short_description'] ?? '');
        $price = (float)($data['price'] ?? 0);
        $level = sanitize($data['level'] ?? 'beginner');
        $thumbnail = $data['thumbnail'] ?? '';
        $status = sanitize($data['status'] ?? 'draft');
        
        $stmt = $this->conn->prepare("INSERT INTO courses (teacher_id, category_id, title, slug, description, short_description, price, level, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return ['success' => false, 'message' => 'Database error'];
        
        $stmt->bind_param("iissssdsss", $teacherId, $categoryId, $title, $slug, $description, $shortDesc, $price, $level, $thumbnail, $status);
        
        if ($stmt->execute()) {
            return ['success' => true, 'course_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to create course'];
    }
    
    private function createSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter++;
        }
        return $slug;
    }
    
    private function slugExists($slug) {
        $stmt = $this->conn->prepare("SELECT id FROM courses WHERE slug = ?");
        if (!$stmt) return false;
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public function update($id, $data) {
        $title = sanitize($data['title']);
        $categoryId = (int)($data['category_id'] ?? 0);
        $description = sanitize($data['description'] ?? '');
        $shortDesc = sanitize($data['short_description'] ?? '');
        $price = (float)($data['price'] ?? 0);
        $level = sanitize($data['level'] ?? 'beginner');
        $status = sanitize($data['status'] ?? 'draft');
        
        $stmt = $this->conn->prepare("UPDATE courses SET title = ?, category_id = ?, description = ?, short_description = ?, price = ?, level = ?, status = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("sissdssi", $title, $categoryId, $description, $shortDesc, $price, $level, $status, $id);
        return $stmt->execute();
    }
    
    public function updateThumbnail($id, $thumbnail) {
        $stmt = $this->conn->prepare("UPDATE courses SET thumbnail = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("si", $thumbnail, $id);
        return $stmt->execute();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image as teacher_image, cat.name as category_name FROM courses c JOIN users u ON c.teacher_id = u.id LEFT JOIN categories cat ON c.category_id = cat.id WHERE c.id = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getBySlug($slug) {
        $stmt = $this->conn->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image as teacher_image, cat.name as category_name FROM courses c JOIN users u ON c.teacher_id = u.id LEFT JOIN categories cat ON c.category_id = cat.id WHERE c.slug = ?");
        if (!$stmt) return null;
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getAll($status = null, $limit = null) {
        $sql = "SELECT c.*, u.first_name, u.last_name, cat.name as category_name, 
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count,
                (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating
                FROM courses c 
                JOIN users u ON c.teacher_id = u.id 
                LEFT JOIN categories cat ON c.category_id = cat.id";
        
        if ($status) {
            $sql .= " WHERE c.status = '" . $this->conn->real_escape_string($status) . "'";
        }
        $sql .= " ORDER BY c.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function getByTeacher($teacherId) {
        $stmt = $this->conn->prepare("SELECT c.*, cat.name as category_name,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count,
                (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating
                FROM courses c 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.teacher_id = ? ORDER BY c.created_at DESC");
        if (!$stmt) return [];
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function search($keyword, $categoryId = null) {
        $keyword = "%$keyword%";
        $sql = "SELECT c.*, u.first_name, u.last_name, cat.name as category_name,
                (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating
                FROM courses c 
                JOIN users u ON c.teacher_id = u.id 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.status = 'published' AND (c.title LIKE ? OR c.description LIKE ?)";
        
        if ($categoryId) {
            $sql .= " AND c.category_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return [];
            $stmt->bind_param("ssi", $keyword, $keyword, $categoryId);
        } else {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return [];
            $stmt->bind_param("ss", $keyword, $keyword);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getFeatured($limit = 6) {
        $sql = "SELECT c.*, u.first_name, u.last_name, cat.name as category_name,
                (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating
                FROM courses c 
                JOIN users u ON c.teacher_id = u.id 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.status = 'published' AND c.is_featured = 1 
                ORDER BY c.created_at DESC LIMIT " . (int)$limit;
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function toggleFeatured($id) {
        $stmt = $this->conn->prepare("UPDATE courses SET is_featured = NOT is_featured WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function updateDuration($courseId) {
        $stmt = $this->conn->prepare("UPDATE courses SET total_duration = (SELECT COALESCE(SUM(duration), 0) FROM videos WHERE course_id = ?) WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("ii", $courseId, $courseId);
        return $stmt->execute();
    }
    
    public function getCategories() {
        $result = $this->conn->query("SELECT * FROM categories ORDER BY name");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
