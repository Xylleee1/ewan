<?php
// ajax/get_notification_count.php
require_once '../includes/db.php';
require_once '../includes/notifications.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$count = get_unread_notification_count($user_id);

echo json_encode(['count' => $count]);
?>
