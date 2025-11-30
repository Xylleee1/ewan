<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/notifications.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'assistant', 'faculty'])) {
    echo "<p>Access denied.</p>";
    exit();
}



// Handle approve/reject/revert BEFORE any output
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    if ($action === 'approve') {
        // Get request details including student_id and email
        $req = mysqli_query($conn, "
            SELECT br.apparatus_id, br.quantity, br.student_id, br.request_id,
                   a.name as apparatus_name,
                   u.email as student_email, 
                   u.full_name as student_name,
                   u.username as student_username
            FROM borrow_requests br
            JOIN apparatus a ON br.apparatus_id = a.apparatus_id
            JOIN users u ON br.student_id = u.user_id
            WHERE br.request_id = $id
        ");
        $req_data = mysqli_fetch_assoc($req);
        
        if ($req_data && !empty($req_data['student_id'])) {
            $apparatus_id = $req_data['apparatus_id'];
            $quantity = $req_data['quantity'];
            $student_id = $req_data['student_id'];
            $student_email = $req_data['student_email'];
            $student_name = $req_data['student_name'] ?: $req_data['student_username'];
            $apparatus_name = $req_data['apparatus_name'];
            
            // Reserve the quantity (reduce available stock)
            $reserve_stmt = mysqli_prepare($conn, "UPDATE apparatus SET quantity = quantity - ? WHERE apparatus_id = ? AND quantity >= ?");
            mysqli_stmt_bind_param($reserve_stmt, 'iii', $quantity, $apparatus_id, $quantity);
            
            if (mysqli_stmt_execute($reserve_stmt)) {
                // Update request status to 'approved' (reserved)
                $stmt = mysqli_prepare($conn, "UPDATE borrow_requests SET status = 'approved' WHERE request_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                add_log($conn, $user_id, "Approve Request", "Approved and reserved apparatus for request #$id");

                // Send bell notification to student
                $notification_title = "Request Approved ✓";
                $notification_message = "Your borrow request #{$id} for {$apparatus_name} (Qty: {$quantity}) has been approved and reserved. Please proceed to the laboratory at your scheduled time.";
                create_notification($student_id, $notification_title, $notification_message, 'success', $id, 'borrow_request');

                // Send email to student
                if (!empty($student_email)) {
                    $email_subject = "CSM Laboratory - Request Approved #" . $id;
                    $email_message = "
                        <p><strong>Great news!</strong> Your borrow request has been <span style='color: #16A34A; font-weight: bold;'>APPROVED</span>.</p>
                        <h3 style='color: #FF6B00;'>Request Details:</h3>
                        <ul style='line-height: 2;'>
                            <li><strong>Request ID:</strong> #{$id}</li>
                            <li><strong>Apparatus:</strong> {$apparatus_name}</li>
                            <li><strong>Quantity:</strong> {$quantity}</li>
                            <li><strong>Status:</strong> <span style='color: #16A34A;'>Approved & Reserved</span></li>
                        </ul>
                        <p style='background: #FFF3CD; padding: 15px; border-left: 4px solid #FFC107; border-radius: 4px;'>
                            <strong>⚠️ Important:</strong> The apparatus has been reserved for you. Please proceed to the laboratory at your scheduled time with your student ID.
                        </p>
                        <p>If you cannot make it at the scheduled time, please inform the laboratory staff immediately.</p>
                    ";
                    send_email_notification($student_email, $student_name, $email_subject, $email_message);
                }

                // Send notification to all admins/assistants
                $admins = mysqli_query($conn, "SELECT user_id, email, full_name FROM users WHERE role IN ('admin', 'assistant')");
                while ($admin = mysqli_fetch_assoc($admins)) {
                    $admin_notification_title = "Request Approved by Faculty";
                    $admin_notification_message = "Request #{$id} for {$apparatus_name} has been approved by " . $_SESSION['full_name'] . " and is ready for release.";
                    create_notification($admin['user_id'], $admin_notification_title, $admin_notification_message, 'info', $id, 'borrow_request');
                }

                header('Location: view_requests.php?success=approved');
            } else {
                header('Location: view_requests.php?error=insufficient_stock');
            }
        } else {
            header('Location: view_requests.php?error=student_not_found');
        }
    } elseif ($action === 'reject') {
        // Get student_id and email first
        $req = mysqli_query($conn, "
            SELECT br.student_id, br.request_id,
                   a.name as apparatus_name,
                   u.email as student_email,
                   u.full_name as student_name,
                   u.username as student_username
            FROM borrow_requests br
            JOIN apparatus a ON br.apparatus_id = a.apparatus_id
            JOIN users u ON br.student_id = u.user_id
            WHERE br.request_id = $id
        ");
        $req_data = mysqli_fetch_assoc($req);
        
        if ($req_data && !empty($req_data['student_id'])) {
            $student_id = $req_data['student_id'];
            $student_email = $req_data['student_email'];
            $student_name = $req_data['student_name'] ?: $req_data['student_username'];
            $apparatus_name = $req_data['apparatus_name'];
            
            $stmt = mysqli_prepare($conn, "UPDATE borrow_requests SET status = 'rejected' WHERE request_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            add_log($conn, $user_id, "Reject Request", "Rejected request #$id");

            // Send bell notification to student
            $notification_title = "Request Rejected ✗";
            $notification_message = "Your borrow request #{$id} for {$apparatus_name} has been rejected. Please contact your instructor or the laboratory office for more information.";
            create_notification($student_id, $notification_title, $notification_message, 'danger', $id, 'borrow_request');

            // Send email to student
            if (!empty($student_email)) {
                $email_subject = "CSM Laboratory - Request Rejected #" . $id;
                $email_message = "
                    <p>We regret to inform you that your borrow request has been <span style='color: #E11D48; font-weight: bold;'>REJECTED</span>.</p>
                    <h3 style='color: #FF6B00;'>Request Details:</h3>
                    <ul style='line-height: 2;'>
                        <li><strong>Request ID:</strong> #{$id}</li>
                        <li><strong>Apparatus:</strong> {$apparatus_name}</li>
                        <li><strong>Status:</strong> <span style='color: #E11D48;'>Rejected</span></li>
                    </ul>
                    <p style='background: #FEE2E2; padding: 15px; border-left: 4px solid #E11D48; border-radius: 4px;'>
                        <strong>ℹ️ Next Steps:</strong> Please visit the laboratory administration office or contact your instructor for clarification regarding your request.
                    </p>
                    <p>If you have any questions, feel free to reach out to us.</p>
                ";
                send_email_notification($student_email, $student_name, $email_subject, $email_message);
            }

            header('Location: view_requests.php?success=rejected');
        } else {
            header('Location: view_requests.php?error=student_not_found');
        }
    } elseif ($action === 'revert') {
        // Revert approved request back to pending and restore quantity
        $req = mysqli_query($conn, "
            SELECT br.apparatus_id, br.quantity, br.status, br.student_id, br.request_id,
                   a.name as apparatus_name,
                   u.email as student_email,
                   u.full_name as student_name,
                   u.username as student_username
            FROM borrow_requests br
            JOIN apparatus a ON br.apparatus_id = a.apparatus_id
            JOIN users u ON br.student_id = u.user_id
            WHERE br.request_id = $id
        ");
        $req_data = mysqli_fetch_assoc($req);
        
        if ($req_data && $req_data['status'] === 'approved' && !empty($req_data['student_id'])) {
            $apparatus_id = $req_data['apparatus_id'];
            $quantity = $req_data['quantity'];
            $student_id = $req_data['student_id'];
            $student_email = $req_data['student_email'];
            $student_name = $req_data['student_name'] ?: $req_data['student_username'];
            $apparatus_name = $req_data['apparatus_name'];
            
            // Restore quantity
            mysqli_query($conn, "UPDATE apparatus SET quantity = quantity + $quantity WHERE apparatus_id = $apparatus_id");
            
            // Revert to pending
            $stmt = mysqli_prepare($conn, "UPDATE borrow_requests SET status = 'pending' WHERE request_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            add_log($conn, $user_id, "Revert Approval", "Reverted approval for request #$id");
            
            // Send bell notification to student
            $notification_title = "Request Approval Reverted";
            $notification_message = "The approval for your request #{$id} for {$apparatus_name} has been reverted and is now pending review again.";
            create_notification($student_id, $notification_title, $notification_message, 'warning', $id, 'borrow_request');
            
            // Send email to student
            if (!empty($student_email)) {
                $email_subject = "CSM Laboratory - Request Approval Reverted #" . $id;
                $email_message = "
                    <p>The approval status of your borrow request has been <span style='color: #F59E0B; font-weight: bold;'>REVERTED</span>.</p>
                    <h3 style='color: #FF6B00;'>Request Details:</h3>
                    <ul style='line-height: 2;'>
                        <li><strong>Request ID:</strong> #{$id}</li>
                        <li><strong>Apparatus:</strong> {$apparatus_name}</li>
                        <li><strong>Quantity:</strong> {$quantity}</li>
                        <li><strong>Status:</strong> <span style='color: #F59E0B;'>Pending Review</span></li>
                    </ul>
                    <p style='background: #FEF3C7; padding: 15px; border-left: 4px solid #F59E0B; border-radius: 4px;'>
                        <strong>ℹ️ What this means:</strong> Your request is now back to pending status and will be reviewed again. You will receive another notification once a decision is made.
                    </p>
                ";
                send_email_notification($student_email, $student_name, $email_subject, $email_message);
            }
            
            header('Location: view_requests.php?success=reverted');
        } else {
            header('Location: view_requests.php?error=cannot_revert');
        }
    }
    exit();
}

require_once __DIR__ . '/includes/header.php';

// Fetch requests based on role
if ($_SESSION['role'] === 'faculty') {
    $fid = $_SESSION['user_id'];
    $res = mysqli_query($conn, "
        SELECT br.*, 
               s.full_name AS student_name,
               s.username AS student_username,
               s.email AS student_email,
               a.name AS apparatus_name,
               a.category AS apparatus_category
        FROM borrow_requests br
        LEFT JOIN users s ON br.student_id = s.user_id
        LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.faculty_id = $fid
        ORDER BY 
            CASE br.status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                ELSE 3 
            END,
            br.date_requested DESC
    ");
} else {
    $res = mysqli_query($conn, "
        SELECT br.*, 
               s.full_name AS student_name,
               s.username AS student_username,
               s.email AS student_email,
               a.name AS apparatus_name,
               a.category AS apparatus_category,
               f.full_name AS faculty_name,
               f.email AS faculty_email
        FROM borrow_requests br
        LEFT JOIN users s ON br.student_id = s.user_id
        LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        LEFT JOIN users f ON br.faculty_id = f.user_id
        ORDER BY 
            CASE br.status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                ELSE 3 
            END,
            br.date_requested DESC
    ");
}

// Calculate statistics
$total = mysqli_num_rows($res);
mysqli_data_seek($res, 0);
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;
$returned_count = 0;
$released_count = 0;

$temp_res = mysqli_query($conn, $_SESSION['role'] === 'faculty'
    ? "SELECT status FROM borrow_requests WHERE faculty_id = {$_SESSION['user_id']}"
    : "SELECT status FROM borrow_requests");

while ($row = mysqli_fetch_assoc($temp_res)) {
    if ($row['status'] == 'pending') $pending_count++;
    elseif ($row['status'] == 'approved') $approved_count++;
    elseif ($row['status'] == 'rejected') $rejected_count++;
    elseif ($row['status'] == 'returned') $returned_count++;
    elseif ($row['status'] == 'released') $released_count++;
}
?>

<style>
body { background: #fff; color: #000; font-family: Arial, sans-serif; }
.page-header {
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 3px solid #FF6F00;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.page-header h2 { font-size: 26px; font-weight: 700; color: #FF6F00; margin: 0; }

.stat-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.stat-box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.stat-box h3 { font-size: 32px; margin: 0; color: #cc5500; }
.stat-box p { margin: 8px 0 0 0; color: #000; font-size: 13px; }

.filter-bar {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
}
.filter-bar input {
    flex: 2;
    padding: 14px 18px;
    border: none;
    border-radius: 10px;
    background: #f0f0f0;
    font-size: 16px;
    outline: none;
}
.filter-bar select {
    flex: 1;
    padding: 10px 12px;
    border: none;
    border-radius: 10px;
    background: #f0f0f0;
    font-size: 14px;
    outline: none;
}

.table-container { overflow-x: auto; border-radius: 12px; }
.table { width: 100%; border-collapse: collapse; background: #f8f8f8; }
.table thead { background: #e0e0e0; color: #333; }
.table th, .table td { padding: 14px 16px; font-size: 14px; text-align: left; vertical-align: middle; }
.table tbody tr { transition: background 0.2s; cursor: pointer; }
.table tbody tr:hover { background: #f1f1f1; }

.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    text-transform: capitalize;
    display: inline-block;
}
.status.pending { background: #FFC107; color: #111827; }
.status.approved { background: #16A34A; color: #fff; }
.status.rejected { background: #E11D48; color: #fff; }
.status.released { background: #0EA5E9; color: #fff; }
.status.returned { background: #7C3AED; color: #fff; }

.action-links { gap: 8px; }
.action-links a, .action-links button {
    padding: 8px 14px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    border: none;
    outline: none;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-approve {
    background: linear-gradient(135deg, #FF6B00, #FF3D00);
    color: #fff;
}
.btn-approve:hover { box-shadow: 0 6px 20px rgba(255,111,0,0.35); transform: translateY(-2px); }
.btn-reject {
    background: #E11D48;
    color: #fff;
}
.btn-reject:hover { background: #C2184B; }
.btn-revert {
    background: #0EA5E9;
    color: #fff;
}
.btn-revert:hover { background: #0284C7; }
.btn-view {
    background: #7C3AED;
    color: #fff;
}
.btn-view:hover { background: #6D28D9; }

.reserved-badge {
    background: #FFF3CD;
    color: #664D03;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.success-message {
    background: #D1FAE5;
    color: #065F46;
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #16A34A;
}

.error-message {
    background: #FEE2E2;
    color: #991B1B;
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #E11D48;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 80%;
    max-width: 700px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    background: linear-gradient(135deg, #FF6B00, #FF3D00);
    color: white;
    padding: 20px 25px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 22px;
}

.close {
    color: white;
    font-size: 32px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s;
}

.close:hover {
    transform: scale(1.1);
}

.modal-body {
    padding: 25px;
}

.detail-row {
    display: grid;
    grid-template-columns: 180px 1fr;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #FF6B00;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-value {
    color: #333;
}

.detail-value.highlight {
    font-weight: 600;
    color: #FF6B00;
}
</style>

<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] === 'approved'): ?>
        <div class="success-message">✓ Request approved and apparatus reserved successfully!</div>
    <?php elseif ($_GET['success'] === 'rejected'): ?>
        <div class="success-message">✓ Request rejected successfully!</div>
    <?php elseif ($_GET['success'] === 'reverted'): ?>
        <div class="success-message">✓ Approval reverted and stock restored successfully!</div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <?php if ($_GET['error'] === 'insufficient_stock'): ?>
        <div class="error-message">✗ Insufficient stock to approve this request!</div>
    <?php elseif ($_GET['error'] === 'cannot_revert'): ?>
        <div class="error-message">✗ Cannot revert this request. Only approved requests can be reverted.</div>
    <?php elseif ($_GET['error'] === 'student_not_found'): ?>
        <div class="error-message">✗ Student information not found. Cannot process request.</div>
    <?php endif; ?>
<?php endif; ?>

<div class="page-header">
    <h2><?= $_SESSION['role'] === 'faculty' ? 'Borrow Requests for Approval' : 'All Borrow Requests' ?></h2>
</div>

<div class="stat-summary">
    <div class="stat-box"><h3><?= $total ?></h3><p>Total Requests</p></div>
    <div class="stat-box"><h3><?= $pending_count ?></h3><p>Pending</p></div>
    <div class="stat-box"><h3><?= $approved_count ?></h3><p>Approved (Reserved)</p></div>
    <div class="stat-box"><h3><?= $released_count ?></h3><p>Released</p></div>
    <div class="stat-box"><h3><?= $rejected_count ?></h3><p>Rejected</p></div>
    <div class="stat-box"><h3><?= $returned_count ?></h3><p>Returned</p></div>
</div>

<div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search student name..." style="flex: 3;">
    <select id="statusFilter">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="released">Released</option>
        <option value="returned">Returned</option>
    </select>
</div>

<div class="table-container">
    <table class="table" id="requestsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Apparatus</th>
                <th>Qty</th>
                <?php if ($_SESSION['role'] !== 'faculty'): ?>
                <th>Faculty</th>
                <?php endif; ?>
                <th>Purpose</th>
                <th>Date Needed</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($res) > 0): ?>
                <?php while($r = mysqli_fetch_assoc($res)): ?>
                <tr data-status="<?= strtolower($r['status']); ?>" onclick="viewDetails(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)">
                    <td><?= $r['request_id']; ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['student_name'] ?: $r['student_username'] ?: $r['student_email'] ?: ''); ?></strong>
                        <?php if (!empty($r['student_email'])): ?>
                            <br><small style="color:#000;"><?= htmlspecialchars($r['student_email']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($r['apparatus_name'] ?? 'N/A'); ?>
                        <br><small style="color:#000;"><?= htmlspecialchars($r['apparatus_category'] ?? ''); ?></small>
                        <?php if ($r['status'] === 'approved'): ?>
                            <span class="reserved-badge">RESERVED</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $r['quantity']; ?></td>
                    <?php if ($_SESSION['role'] !== 'faculty'): ?>
                    <td><?= htmlspecialchars($r['faculty_name'] ?? ''); ?></td>
                    <?php endif; ?>
                    <td>
                        <?= htmlspecialchars($r['purpose'] ?? 'N/A'); ?>
                    </td>
                    <td>
                        <?= date('M d, Y', strtotime($r['date_needed'])); ?>
                        <br><small style="color:#000;"><?= htmlspecialchars($r['time_from'])." - ".htmlspecialchars($r['time_to']); ?></small>
                    </td>
                    <td><span class="status <?= strtolower($r['status']); ?>"><?= htmlspecialchars($r['status']); ?></span></td>
                    <td class="action-links" onclick="event.stopPropagation();">
                        <?php if ($r['status'] === 'pending'): ?>
                            <a href="view_requests.php?action=approve&id=<?= $r['request_id']; ?>" 
                               class="btn-approve" 
                               onclick="return confirm('Approve and reserve this apparatus?');">Approve</a>
                            <a href="view_requests.php?action=reject&id=<?= $r['request_id']; ?>" 
                               class="btn-reject" 
                               onclick="return confirm('Reject this request?');">Reject</a>
                        <?php elseif ($r['status'] === 'approved'): ?>
                            <a href="view_requests.php?action=revert&id=<?= $r['request_id']; ?>" 
                               class="btn-revert" 
                               onclick="return confirm('Revert this approval? Stock will be restored.');">Revert</a>
                        <?php else: ?>
                            <span style="color: #555; font-size: 13px;">No actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="<?= $_SESSION['role'] === 'faculty' ? '8' : '9'; ?>" class="empty-state">No borrow requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="bi bi-info-circle"></i> Request Details</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Details will be inserted here -->
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#requestsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.dataset.status;
        const matchesSearch = text.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

function viewDetails(data) {
    const modal = document.getElementById('detailsModal');
    const modalBody = document.getElementById('modalBody');
    
    const statusClass = data.status.toLowerCase();
    const statusBadge = `<span class="status ${statusClass}">${data.status}</span>`;
    
    modalBody.innerHTML = `
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-hash"></i> Request ID</div>
            <div class="detail-value highlight">#${data.request_id}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-person"></i> Student</div>
            <div class="detail-value">${data.student_name || data.student_username || 'N/A'}<br><small>${data.student_email || ''}</small></div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-box-seam"></i> Apparatus</div>
            <div class="detail-value">${data.apparatus_name || 'N/A'}<br><small>${data.apparatus_category || ''}</small></div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-123"></i> Quantity</div>
            <div class="detail-value highlight">${data.quantity}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-droplet"></i> Concentration/Size</div>
            <div class="detail-value">${data.concentration || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-calendar-event"></i> Date Needed</div>
            <div class="detail-value">${new Date(data.date_needed).toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-clock"></i> Time</div>
            <div class="detail-value">${data.time_from} - ${data.time_to}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-calendar3"></i> Schedule</div>
            <div class="detail-value">${data.schedule || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-book"></i> Subject</div>
            <div class="detail-value">${data.subject || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-building"></i> Room</div>
            <div class="detail-value">${data.room || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-pencil-square"></i> Purpose</div>
            <div class="detail-value">${data.purpose || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-calendar-check"></i> Date Requested</div>
            <div class="detail-value">${new Date(data.date_requested).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="bi bi-flag"></i> Status</div>
            <div class="detail-value">${statusBadge}</div>
        </div>
    `;
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>