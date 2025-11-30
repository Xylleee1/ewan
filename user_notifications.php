<?php
// user_notifications.php - User notifications page
require_once 'includes/header.php';
require_once 'includes/notifications.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get user email for sending notifications
$user_query = mysqli_query($conn, "SELECT email, full_name FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);
$user_email = $user_data['email'];
$user_name = $user_data['full_name'];

// Get notifications
$result = mysqli_query($conn, "
    SELECT SQL_CALC_FOUND_ROWS * FROM user_notifications
    WHERE user_id = $user_id
    ORDER BY created_at DESC
    LIMIT $offset, $per_page
");

$total_result = mysqli_query($conn, "SELECT FOUND_ROWS() as total");
$total_row = mysqli_fetch_assoc($total_result);
$total_notifications = $total_row['total'];
$total_pages = ceil($total_notifications / $per_page);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}

// Get unread count
$unread_result = mysqli_query($conn, "SELECT COUNT(*) as unread_count FROM user_notifications WHERE user_id = $user_id AND is_read = 0");
$unread_row = mysqli_fetch_assoc($unread_result);
$unread_count = $unread_row['unread_count'];

// Send email for unread notifications (if user has email)
if (!empty($user_email) && $unread_count > 0) {
    // Get unread notifications that haven't been emailed yet
    $unread_notifications = mysqli_query($conn, "
        SELECT * FROM user_notifications 
        WHERE user_id = $user_id 
        AND is_read = 0 
        ORDER BY created_at DESC
        LIMIT 5
    ");
    
    if (mysqli_num_rows($unread_notifications) > 0) {
        // Prepare email content
        $email_subject = "CSM Apparatus System - You have " . $unread_count . " new notification(s)";
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #FF6F00, #FFA040); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .notification { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #FF6F00; border-radius: 5px; }
                .notification h3 { margin: 0 0 10px 0; color: #FF6F00; font-size: 16px; }
                .notification p { margin: 5px 0; color: #555; }
                .notification small { color: #999; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🔔 CSM Apparatus System Notifications</h2>
                </div>
                <div style='padding: 20px; background: white;'>
                    <p>Dear <strong>" . htmlspecialchars($user_name) . "</strong>,</p>
                    <p>You have <strong>" . $unread_count . "</strong> unread notification(s):</p>
        ";
        
        while ($notif = mysqli_fetch_assoc($unread_notifications)) {
            $email_body .= "
                <div class='notification'>
                    <h3>" . htmlspecialchars($notif['title']) . "</h3>
                    <p>" . htmlspecialchars($notif['message']) . "</p>
                    <small>" . date('M d, Y \a\t h:i A', strtotime($notif['created_at'])) . "</small>
                </div>
            ";
        }
        
        $email_body .= "
                    <p style='margin-top: 20px;'>
                        <a href='" . $_SERVER['HTTP_HOST'] . "/user_notifications.php' 
                           style='background: #FF6F00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            View All Notifications
                        </a>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from CSM Apparatus Borrowing System.</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send email using PHP mail function
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: CSM Laboratory <noreply@csm-lab.com>" . "\r\n";
        
        // Uncomment to actually send emails
        // mail($user_email, $email_subject, $email_body, $headers);
    }
}
?>

<style>
body {
    background: #F9FAFB;
}

.notifications-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 24px;
}

.notifications-header {
    background: white;
    padding: 32px;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #E5E7EB;
}

.notifications-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 24px;
}

.notifications-header h2 {
    font-size: 32px;
    font-weight: 700;
    background: linear-gradient(135deg, #FF6F00, #FFA040);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.notifications-header h2 i {
    background: linear-gradient(135deg, #FF6F00, #FFA040);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.notifications-header p {
    color: #6B7280;
    font-size: 15px;
    margin: 0;
    font-weight: 400;
}

.unread-badge {
    background: linear-gradient(135deg, #FF6F00, #FF3D00);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(255, 111, 0, 0.3);
}

.btn-mark-read {
    background: white;
    color: #FF6F00;
    border: 2px solid #FF6F00;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-mark-read:hover {
    background: linear-gradient(135deg, #FF6F00, #FF3D00);
    color: white;
    border-color: #FF6F00;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 111, 0, 0.3);
}

.empty-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #E5E7EB;
}

.empty-state i {
    font-size: 80px;
    color: #D1D5DB;
    margin-bottom: 24px;
    display: block;
}

.empty-state h4 {
    font-size: 24px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 15px;
    color: #6B7280;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.6;
}

.notifications-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid #E5E7EB;
}

.notification-item {
    padding: 24px 28px;
    border-bottom: 1px solid #F3F4F6;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
    background: white;
    position: relative;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: #FFFBF0;
    border-left-color: #FBBF24;
}

.notification-item.unread {
    background: #FFF7ED;
    border-left-color: #FF6F00;
}

.notification-item.unread:hover {
    background: #FFEDD5;
    border-left-color: #EA580C;
}

.notification-content {
    display: flex;
    flex-direction: column;
    gap: 14px;
    cursor: pointer;
}

.notification-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.notification-title-group {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.notification-title {
    font-size: 17px;
    font-weight: 600;
    color: #1F2937;
    margin: 0;
    line-height: 1.4;
}

.notification-item.unread .notification-title {
    font-weight: 700;
    color: #111827;
}

.badge-new {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white;
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.notification-message {
    font-size: 15px;
    color: #4B5563;
    line-height: 1.7;
    margin: 0;
}

.notification-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 4px;
}

.notification-time {
    font-size: 14px;
    color: #9CA3AF;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.notification-time i {
    font-size: 14px;
}

.btn-view-details {
    background: linear-gradient(135deg, #FF6F00, #FF3D00);
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(255, 111, 0, 0.25);
}

.btn-view-details:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 111, 0, 0.35);
    color: white;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 32px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item {
    display: flex;
}

.page-link {
    padding: 12px 18px;
    border: 2px solid #E5E7EB;
    background: white;
    color: #6B7280;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 48px;
}

.page-link:hover {
    background: #FFF7ED;
    border-color: #FF6F00;
    color: #FF6F00;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #FF6F00, #FF3D00);
    border-color: #FF6F00;
    color: white;
    box-shadow: 0 2px 8px rgba(255, 111, 0, 0.3);
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
    background: #F9FAFB;
}

/* Responsive Design */
@media (max-width: 768px) {
    .notifications-wrapper {
        padding: 24px 16px;
    }

    .notifications-header {
        padding: 24px 20px;
    }

    .notifications-header h2 {
        font-size: 26px;
    }

    .notifications-header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }

    .notification-item {
        padding: 20px 18px;
    }

    .notification-title {
        font-size: 16px;
    }

    .notification-message {
        font-size: 14px;
    }

    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .btn-view-details {
        width: 100%;
        justify-content: center;
    }

    .page-link {
        padding: 10px 14px;
        font-size: 13px;
        min-width: 42px;
    }
}
</style>

<div class="notifications-wrapper">
    <!-- Header -->
    <div class="notifications-header">
        <div class="notifications-header-content">
            <div>
                <h2><i class="bi bi-bell-fill"></i> Notifications</h2>
                <p>Stay updated with your borrowing activities</p>
                <?php if ($unread_count > 0): ?>
                    <div style="margin-top: 16px;">
                        <span class="unread-badge">
                            <i class="bi bi-dot"></i>
                            <?= $unread_count ?> Unread
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($notifications)): ?>
                <button class="btn-mark-read" onclick="markAllAsRead()">
                    <i class="bi bi-check-all"></i> Mark All Read
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($notifications)): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="bi bi-bell-slash"></i>
            <h4>No notifications yet</h4>
            <p>You'll receive notifications about your borrowing requests and activities here. Check back later for updates.</p>
        </div>
    <?php else: ?>
        <!-- Notifications List -->
        <div class="notifications-card">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>"
                     data-id="<?= $notification['notification_id'] ?>"
                     data-related-id="<?= $notification['related_id'] ?? '' ?>"
                     data-related-type="<?= $notification['related_type'] ?? '' ?>">
                    <div class="notification-content" onclick="handleNotificationClick(this)">
                        <div class="notification-header">
                            <div class="notification-title-group">
                                <h6 class="notification-title">
                                    <?= htmlspecialchars($notification['title']) ?>
                                </h6>
                                <?php if (!$notification['is_read']): ?>
                                    <span class="badge-new">New</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="notification-message">
                            <?= htmlspecialchars($notification['message']) ?>
                        </p>
                        
                        <div class="notification-footer">
                            <span class="notification-time">
                                <i class="bi bi-clock"></i>
                                <?= date('M d, Y \a\t h:i A', strtotime($notification['created_at'])) ?>
                            </span>
                            <?php if ($notification['related_id'] && $notification['related_type']): ?>
                                <a href="#" class="btn-view-details"
                                   onclick="event.stopPropagation(); viewRelated(<?= $notification['related_id'] ?>, '<?= $notification['related_type'] ?>'); return false;">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    View Details
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Notifications pagination" class="pagination-wrapper">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages ?>"><?= $total_pages ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Handle notification container click
function handleNotificationClick(contentElement) {
    const notificationItem = contentElement.closest('.notification-item');
    const notificationId = notificationItem.getAttribute('data-id');
    const relatedId = notificationItem.getAttribute('data-related-id');
    const relatedType = notificationItem.getAttribute('data-related-type');
    
    // Mark as read
    if (notificationId) {
        markNotificationAsRead(notificationId, notificationItem);
    }
    
    // If there's a related item, navigate to it
    if (relatedId && relatedType) {
        setTimeout(() => {
            viewRelated(relatedId, relatedType);
        }, 200);
    }
}

// Mark notification as read
function markNotificationAsRead(notificationId, notificationItem) {
    fetch('api/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread styling
            notificationItem.classList.remove('unread');
            const badge = notificationItem.querySelector('.badge-new');
            if (badge) badge.remove();
            
            // Update unread count
            const unreadBadge = document.querySelector('.unread-badge');
            if (unreadBadge) {
                const countElement = unreadBadge.textContent;
                const currentCount = parseInt(countElement.match(/\d+/)[0]);
                if (currentCount > 1) {
                    unreadBadge.innerHTML = `<i class="bi bi-dot"></i>${currentCount - 1} Unread`;
                } else {
                    unreadBadge.remove();
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Mark all notifications as read
function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('api/mark_all_notifications_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to mark notifications as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

// View related item
function viewRelated(relatedId, relatedType) {
    switch (relatedType) {
        case 'borrow_request':
            window.location.href = 'request_tracker.php?request_id=' + relatedId;
            break;
        case 'penalty':
            window.location.href = 'student_penalties.php';
            break;
        default:
            console.log('Unknown related type:', relatedType);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>