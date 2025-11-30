<?php
// includes/notifications.php - Complete Notification System with Email Support

/**
 * Create notification for user
 */
function create_notification($user_id, $title, $message, $type = 'info', $related_id = null, $related_type = null) {
    global $conn;
    
    $user_id = intval($user_id);
    $stmt = mysqli_prepare($conn, "INSERT INTO user_notifications (user_id, title, message, type, related_id, related_type) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssis", $user_id, $title, $message, $type, $related_id, $related_type);
    $result = mysqli_stmt_execute($stmt);
    $notification_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    
    // Auto-send email if user has email configured
    if ($result) {
        $user_query = mysqli_query($conn, "SELECT email, full_name FROM users WHERE user_id = $user_id");
        if ($user_data = mysqli_fetch_assoc($user_query)) {
            if (!empty($user_data['email'])) {
                send_email_notification($user_data['email'], $user_data['full_name'], $title, $message);
            }
        }
    }
    
    return $notification_id;
}

/**
 * Get unread notification count for user
 */
function get_unread_notification_count($user_id) {
    global $conn;
    
    $user_id = intval($user_id);
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM user_notifications WHERE user_id = ? AND is_read = 0");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $row['count'];
}

/**
 * Get notifications for user
 */
function get_user_notifications($user_id, $limit = 20) {
    global $conn;
    
    $user_id = intval($user_id);
    $limit = intval($limit);
    
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM user_notifications
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT ?
    ");
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    return $notifications;
}

/**
 * Mark notification as read
 */
function mark_notification_read($notification_id, $user_id) {
    global $conn;
    
    $notification_id = intval($notification_id);
    $user_id = intval($user_id);
    
    $stmt = mysqli_prepare($conn, "UPDATE user_notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $notification_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/**
 * Mark all notifications as read for user
 */
function mark_all_notifications_read($user_id) {
    global $conn;
    
    $user_id = intval($user_id);
    $stmt = mysqli_prepare($conn, "UPDATE user_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/**
 * Send email notification using PHP mail() function
 * For production, integrate with PHPMailer or SMTP service
 */
function send_email_notification($to_email, $to_name, $subject, $message, $from_name = 'CSM Laboratory System') {
    // Email configuration - Update these for your SMTP server
    $from_email = 'noreply@csm-lab.edu.ph';
    
    // Build HTML email
    $html_message = build_email_template($to_name, $subject, $message);
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $from_name <$from_email>" . "\r\n";
    $headers .= "Reply-To: $from_email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    $success = @mail($to_email, $subject, $html_message, $headers);
    
    // Log email attempt
    if (!$success) {
        error_log("Failed to send email to: $to_email - Subject: $subject");
    }
    
    return $success;
}

/**
 * Build email template
 */
function build_email_template($to_name, $subject, $message) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$subject</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #FF6F00, #FFA040); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px 20px; }
            .message { background: #f9f9f9; padding: 20px; border-left: 4px solid #FF6F00; border-radius: 4px; margin: 20px 0; }
            .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            .button { display: inline-block; background: #FF6F00; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔬 CSM Apparatus System</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
                <div class='message'>
                    $message
                </div>
                <p>If you have any questions, please contact the laboratory administration.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message from CSM Laboratory System.</p>
                <p>Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " College of Science and Mathematics</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Generate notification on borrow request submission
 */
function notify_borrow_request_submitted($request_id) {
    global $conn;
    
    $request_id = intval($request_id);
    
    $request = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT br.*, u.full_name, u.email, f.full_name as faculty_name, a.name as apparatus_name
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        LEFT JOIN users f ON br.faculty_id = f.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.request_id = $request_id
    "));
    
    if ($request && $request['faculty_id']) {
        // Notify faculty
        create_notification(
            $request['faculty_id'],
            'New Borrow Request',
            "{$request['full_name']} has requested {$request['apparatus_name']} (Qty: {$request['quantity']}) for {$request['subject']}.",
            'info',
            $request_id,
            'borrow_request'
        );
        
        // Notify admins
        $admins = mysqli_query($conn, "SELECT user_id FROM users WHERE role IN ('admin', 'assistant')");
        while ($admin = mysqli_fetch_assoc($admins)) {
            create_notification(
                $admin['user_id'],
                'New Borrow Request',
                "New request from {$request['full_name']} for {$request['apparatus_name']}.",
                'info',
                $request_id,
                'borrow_request'
            );
        }
    }
}

/**
 * Generate notification on request approval
 */
function notify_request_approved($request_id) {
    global $conn;
    
    $request_id = intval($request_id);
    
    $request = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT br.*, u.full_name, u.email, a.name as apparatus_name
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.request_id = $request_id
    "));
    
    if ($request) {
        $message = "Your request for <strong>{$request['apparatus_name']}</strong> (Qty: {$request['quantity']}) has been <span style='color: #16A34A; font-weight: bold;'>APPROVED</span>.<br><br>Please proceed to the laboratory at your scheduled time with your student ID.";
        
        create_notification(
            $request['student_id'],
            'Request Approved ✓',
            strip_tags($message),
            'success',
            $request_id,
            'borrow_request'
        );
    }
}

/**
 * Generate notification on request denial
 */
function notify_request_denied($request_id) {
    global $conn;
    
    $request_id = intval($request_id);
    
    $request = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT br.*, u.full_name, u.email, a.name as apparatus_name
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.request_id = $request_id
    "));
    
    if ($request) {
        create_notification(
            $request['student_id'],
            'Request Rejected',
            "Your request for {$request['apparatus_name']} has been rejected. Please contact the laboratory office for clarification.",
            'danger',
            $request_id,
            'borrow_request'
        );
    }
}

/**
 * Generate notification on apparatus return
 */
function notify_apparatus_returned($request_id) {
    global $conn;
    
    $request_id = intval($request_id);
    
    $request = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT br.*, u.full_name, u.email, a.name as apparatus_name
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.request_id = $request_id
    "));
    
    if ($request) {
        create_notification(
            $request['student_id'],
            'Return Confirmed',
            "Your borrowed {$request['apparatus_name']} has been successfully returned. Thank you!",
            'success',
            $request_id,
            'borrow_request'
        );
    }
}

/**
 * Generate notification for penalty
 */
function notify_penalty_issued($penalty_id) {
    global $conn;
    
    $penalty_id = intval($penalty_id);
    
    $penalty = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, t.request_id, br.student_id, u.full_name, a.name as apparatus_name
        FROM penalties p
        JOIN transactions t ON p.transaction_id = t.transaction_id
        JOIN borrow_requests br ON t.request_id = br.request_id
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE p.penalty_id = $penalty_id
    "));
    
    if ($penalty) {
        create_notification(
            $penalty['student_id'],
            'Penalty Issued',
            "A penalty of ₱{$penalty['amount']} has been issued. Reason: {$penalty['reason']}",
            'warning',
            $penalty_id,
            'penalty'
        );
    }
}

/**
 * Send due date reminders
 */
function send_due_date_reminders() {
    global $conn;
    
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $due_items = mysqli_query($conn, "
        SELECT br.request_id, br.student_id, br.date_needed, u.full_name, a.name as apparatus_name
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.status IN ('approved', 'released')
        AND br.date_needed = '$tomorrow'
    ");
    
    while ($item = mysqli_fetch_assoc($due_items)) {
        create_notification(
            $item['student_id'],
            'Due Date Reminder',
            "Your borrowed {$item['apparatus_name']} is due tomorrow. Please return on time.",
            'warning',
            $item['request_id'],
            'borrow_request'
        );
    }
    
    // Overdue items
    $today = date('Y-m-d');
    $overdue_items = mysqli_query($conn, "
        SELECT br.request_id, br.student_id, br.date_needed, u.full_name, a.name as apparatus_name,
               DATEDIFF('$today', br.date_needed) as days_overdue
        FROM borrow_requests br
        JOIN users u ON br.student_id = u.user_id
        JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.status IN ('approved', 'released')
        AND br.date_needed < '$today'
    ");
    
    while ($item = mysqli_fetch_assoc($overdue_items)) {
        create_notification(
            $item['student_id'],
            'OVERDUE NOTICE',
            "Your {$item['apparatus_name']} is {$item['days_overdue']} day(s) overdue. Please return immediately to avoid penalties.",
            'danger',
            $item['request_id'],
            'borrow_request'
        );
    }
}

/**
 * Send custom email to selected users
 */
function send_custom_email_to_users($user_ids, $subject, $message) {
    global $conn;
    
    $ids = implode(',', array_map('intval', $user_ids));
    $users = mysqli_query($conn, "SELECT user_id, full_name, email FROM users WHERE user_id IN ($ids) AND email IS NOT NULL");
    
    $sent_count = 0;
    while ($user = mysqli_fetch_assoc($users)) {
        if (send_email_notification($user['email'], $user['full_name'], $subject, $message)) {
            $sent_count++;
        }
    }
    
    return $sent_count;
}

/**
 * Send custom email to users by role
 */
function send_custom_email_by_role($roles, $subject, $message) {
    global $conn;
    
    $role_list = "'" . implode("','", array_map(function($role) use ($conn) {
        return mysqli_real_escape_string($conn, $role);
    }, $roles)) . "'";
    
    $users = mysqli_query($conn, "SELECT user_id, full_name, email FROM users WHERE role IN ($role_list) AND email IS NOT NULL");
    
    $sent_count = 0;
    while ($user = mysqli_fetch_assoc($users)) {
        if (send_email_notification($user['email'], $user['full_name'], $subject, $message)) {
            $sent_count++;
        }
    }
    
    return $sent_count;
}