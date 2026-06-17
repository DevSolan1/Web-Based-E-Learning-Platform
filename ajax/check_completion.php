<?php
require_once '../config/config.php';
require_once '../classes/Video.php';
require_once '../classes/Enrollment.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['completed' => false]);
    exit;
}

$courseId = $_GET['course_id'] ?? 0;

$video = new Video();
$progress = $video->getCourseProgress($_SESSION['user_id'], $courseId);

$completed = $progress['total_videos'] > 0 && $progress['completed_videos'] >= $progress['total_videos'];

if ($completed) {
    $enrollment = new Enrollment();
    $enrollment->markCompleted($_SESSION['user_id'], $courseId);
}

echo json_encode(['completed' => $completed, 'progress' => $progress]);
