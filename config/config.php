<?php
session_start();

define('SITE_NAME', 'E-Learning Platform');
define('SITE_URL', 'http://localhost/elearning');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('VIDEO_PATH', __DIR__ . '/../uploads/videos/');
define('THUMBNAIL_PATH', __DIR__ . '/../uploads/thumbnails/');
define('CERT_PATH', __DIR__ . '/../uploads/certificates/');

// Create upload directories if they don't exist
$dirs = [UPLOAD_PATH, VIDEO_PATH, THUMBNAIL_PATH, CERT_PATH];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

require_once __DIR__ . '/database.php';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
