<?php
// Script to fix missing database columns
require_once 'config/config.php';

echo "<h2>Database Fix</h2>";

try {
    $db = new Database();
    $conn = $db->connect();
    echo "✓ Database connection successful<br><br>";
    
    // Check if is_approved column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_approved'");
    
    if ($result->num_rows == 0) {
        echo "Adding missing 'is_approved' column...<br>";
        $conn->query("ALTER TABLE users ADD COLUMN is_approved TINYINT(1) DEFAULT 1 AFTER is_active");
        echo "✓ Column 'is_approved' added successfully<br>";
    } else {
        echo "✓ Column 'is_approved' already exists<br>";
    }
    
    // Check if cv_file column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'cv_file'");
    
    if ($result->num_rows == 0) {
        echo "Adding missing 'cv_file' column...<br>";
        $conn->query("ALTER TABLE users ADD COLUMN cv_file VARCHAR(255) AFTER is_approved");
        echo "✓ Column 'cv_file' added successfully<br>";
    } else {
        echo "✓ Column 'cv_file' already exists<br>";
    }
    
    // Update admin user to ensure it's approved
    $conn->query("UPDATE users SET is_approved = 1 WHERE email = 'admin@elearning.com'");
    echo "✓ Admin user set as approved<br><br>";
    
    echo "<h3>✅ Database fixed! You can now login with:</h3>";
    echo "Email: <strong>admin@elearning.com</strong><br>";
    echo "Password: <strong>admin123</strong><br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>