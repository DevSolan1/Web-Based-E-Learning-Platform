<?php
// Debug script to check admin user and create if needed
require_once 'config/config.php';
require_once 'classes/User.php';

echo "<h2>Admin Login Debug</h2>";

try {
    $db = new Database();
    $conn = $db->connect();
    echo "✓ Database connection successful<br><br>";
    
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = 'admin@elearning.com'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "<h3>Admin User Found:</h3>";
        echo "ID: " . $admin['id'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Role: " . $admin['role'] . "<br>";
        echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
        echo "Approved: " . ($admin['is_approved'] ? 'Yes' : 'No') . "<br>";
        echo "Password Hash: " . substr($admin['password'], 0, 20) . "...<br><br>";
        
        // Test password verification
        if (password_verify('admin123', $admin['password'])) {
            echo "✓ Password 'admin123' is correct<br>";
        } else {
            echo "✗ Password 'admin123' does NOT match<br>";
            echo "Updating password...<br>";
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@elearning.com'");
            $updateStmt->bind_param("s", $newHash);
            if ($updateStmt->execute()) {
                echo "✓ Password updated successfully<br>";
            }
        }
    } else {
        echo "✗ Admin user NOT found. Creating...<br>";
        
        $user = new User();
        $adminData = [
            'email' => 'admin@elearning.com',
            'password' => 'admin123',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_approved' => 1
        ];
        
        $result = $user->register($adminData);
        
        if ($result['success']) {
            echo "✓ Admin user created successfully!<br>";
        } else {
            echo "✗ Error creating admin: " . $result['message'] . "<br>";
        }
    }
    
    echo "<br><h3>Login Credentials:</h3>";
    echo "Email: <strong>admin@elearning.com</strong><br>";
    echo "Password: <strong>admin123</strong><br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>