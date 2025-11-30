<?php
// ajax/mark_notification_read.php
require_once '../includes/db.php';
require_once '../includes/notifications.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = (int)$_POST['notification_id'];
    $user_id = $_SESSION['user_id'];

    // Mark notification as read
    mark_notification_read($notification_id, $user_id);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
