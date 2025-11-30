</main>

<footer class="footer text-white text-center py-3 mt-auto" 
        style="background-color: #ff7a00;">
  <small>© 2025 College of Science and Mathematics — Apparatus Borrowing System</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Notification JavaScript -->
<script>
// Get user role for navigation logic
const userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';
// Handle notification click in header dropdown
function handleHeaderNotificationClick(notificationId, relatedId, relatedType) {
    // Mark as read
    fetch('ajax/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                const badge = notificationItem.querySelector('.badge');
                if (badge) badge.remove();
            }
            // Update badge count
            updateNotificationBadge();

            // Navigate to related item if exists
            if (relatedId && relatedType) {
                setTimeout(() => {
                    viewRelated(relatedId, relatedType);
                }, 200);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Mark notification as read (legacy function for backward compatibility)
function viewNotification(notificationId) {
    handleHeaderNotificationClick(notificationId, '', '');
}

// View related item
function viewRelated(relatedId, relatedType) {
    switch (relatedType) {
        case 'borrow_request':
            if (userRole === 'student') {
                window.location.href = 'request_tracker.php?request_id=' + relatedId;
            } else {
                window.location.href = 'view_requests.php';
            }
            break;
        case 'penalty':
            if (userRole === 'student') {
                window.location.href = 'student_penalties.php';
            } else {
                window.location.href = 'penalties.php';
            }
            break;
        default:
            console.log('Unknown related type:', relatedType);
    }
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('ajax/mark_all_notifications_read.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            });
            // Update badge count
            updateNotificationBadge();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Update notification badge count
function updateNotificationBadge() {
    fetch('ajax/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.badge.bg-danger');
        if (data.count > 0) {
            if (badge) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
            } else {
                // Create badge if it doesn't exist
                const bellIcon = document.querySelector('#notificationMenu i');
                if (bellIcon) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    newBadge.style.fontSize = '10px';
                    newBadge.textContent = data.count > 99 ? '99+' : data.count;
                    bellIcon.parentElement.appendChild(newBadge);
                }
            }
        } else {
            if (badge) badge.remove();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Auto-refresh notifications every 30 seconds
setInterval(updateNotificationBadge, 30000);
</script>
</body>
</html>

