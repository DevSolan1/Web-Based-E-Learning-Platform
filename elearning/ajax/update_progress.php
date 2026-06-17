<?php
require_once '../config/config.php';
require_once '../classes/Video.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['video_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$video = new Video();
$watchedDuration = $data['watched_duration'] ?? 0;
$isCompleted = $data['is_completed'] ?? false;

$result = $video->updateProgress($_SESSION['user_id'], $data['video_id'], $watchedDuration, $isCompleted);

echo json_encode(['success' => $result]);
