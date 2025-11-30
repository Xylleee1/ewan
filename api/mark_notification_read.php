<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['notification_id'])) {
        $notification_id = intval($input['notification_id']);
        $user_id = intval($_SESSION['user_id']);
        
        $stmt = mysqli_prepare($conn, "UPDATE user_notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $notification_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing notification_id']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}