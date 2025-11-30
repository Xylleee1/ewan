<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "<p class='text-center mt-5'>Access denied.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit();
}

$student_id = $_SESSION['user_id'];

// Get success message from session
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Fetch all borrow requests for this student with apparatus details
$query = "SELECT br.*, a.name as apparatus_name, a.category, u.full_name as faculty_name
          FROM borrow_requests br
          LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
          LEFT JOIN users u ON br.faculty_id = u.user_id
          WHERE br.student_id = $student_id
          ORDER BY br.date_requested DESC, br.request_id DESC";

$requests = mysqli_query($conn, $query);
?>

<style>
.tracker-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.tracker-header {
    text-align: center;
    margin-bottom: 40px;
}

.tracker-header h2 {
    color: #FF6B00;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}

.tracker-header p {
    color: #666;
    font-size: 16px;
}

.success-banner {
    background: linear-gradient(135deg, #16A34A 0%, #15803D 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 14px;
    margin-bottom: 30px;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
    animation: slideDown 0.4s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-banner i {
    font-size: 24px;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 12px 24px;
    background: #f5f5f5;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    color: #333;
}

.filter-tab:hover {
    border-color: #FFB380;
    background: #fff;
}

.filter-tab.active {
    background: linear-gradient(135deg, #FF6B00 0%, #FF3D00 100%);
    color: white;
    border-color: #FF6B00;
}

.requests-grid {
    display: grid;
    gap: 20px;
}

.request-card {
    background: white;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 5px solid #e0e0e0;
}

.request-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.request-card.pending {
    border-left-color: #FFC107;
}

.request-card.approved {
    border-left-color: #16A34A;
}

.request-card.rejected {
    border-left-color: #E11D48;
}

.request-card.returned {
    border-left-color: #6B7280;
}

.request-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.request-title {
    flex: 1;
}

.request-title h3 {
    color: #111827;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.request-title .category-badge {
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 6px;
    background: #f3f4f6;
    color: #6b7280;
    font-weight: 600;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-badge.pending {
    background: #FFF3CD;
    color: #856404;
}

.status-badge.approved {
    background: #D1FAE5;
    color: #065F46;
}

.status-badge.rejected {
    background: #FEE2E2;
    color: #991B1B;
}

.status-badge.returned {
    background: #E5E7EB;
    color: #374151;
}

.request-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
}

.detail-value {
    font-size: 15px;
    color: #111827;
    font-weight: 500;
}

.detail-value i {
    color: #FF6B00;
    margin-right: 5px;
}

.request-footer {
    border-top: 1px solid #e5e7eb;
    padding-top: 15px;
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.request-date {
    font-size: 13px;
    color: #6b7280;
}

.request-actions {
    display: flex;
    gap: 10px;
}

.btn-view {
    padding: 8px 16px;
    background: #FF6B00;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #FF3D00;
    transform: translateY(-2px);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #6b7280;
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #9ca3af;
    margin-bottom: 25px;
}

.btn-new-request {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #FF6B00 0%, #FF3D00 100%);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-new-request:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,107,0,0.4);
    color: white;
}

@media (max-width: 768px) {
    .request-details {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="tracker-container">
    <div class="tracker-header">
        <h2><i class="bi bi-clipboard-check"></i> Request Tracker</h2>
        <p>Track your borrow requests and their status</p>
    </div>

    <?php if ($success_message): ?>
        <div class="success-banner">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($success_message) ?></span>
        </div>
    <?php endif; ?>

    <div class="filter-tabs">
        <div class="filter-tab active" data-filter="all">All Requests</div>
        <div class="filter-tab" data-filter="pending">Pending</div>
        <div class="filter-tab" data-filter="approved">Approved</div>
        <div class="filter-tab" data-filter="rejected">Rejected</div>
        <div class="filter-tab" data-filter="returned">Returned</div>
    </div>

    <div class="requests-grid">
        <?php if (mysqli_num_rows($requests) > 0): ?>
            <?php while ($req = mysqli_fetch_assoc($requests)): ?>
                <div class="request-card <?= strtolower($req['status']) ?>" data-status="<?= strtolower($req['status']) ?>">
                    <div class="request-header">
                        <div class="request-title">
                            <h3>
                                <i class="bi bi-box-seam"></i>
                                <?= htmlspecialchars($req['apparatus_name']) ?>
                                <span class="category-badge"><?= htmlspecialchars($req['category']) ?></span>
                            </h3>
                        </div>
                        <span class="status-badge <?= strtolower($req['status']) ?>">
                            <?php if ($req['status'] === 'pending'): ?>
                                <i class="bi bi-clock-history"></i>
                            <?php elseif ($req['status'] === 'approved'): ?>
                                <i class="bi bi-check-circle-fill"></i>
                            <?php elseif ($req['status'] === 'rejected'): ?>
                                <i class="bi bi-x-circle-fill"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-return-left"></i>
                            <?php endif; ?>
                            <?= ucfirst($req['status']) ?>
                        </span>
                    </div>

                    <div class="request-details">
                        <div class="detail-item">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">
                                <i class="bi bi-boxes"></i>
                                <?= htmlspecialchars($req['quantity']) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Concentration/Size</span>
                            <span class="detail-value">
                                <i class="bi bi-droplet-half"></i>
                                <?= htmlspecialchars($req['concentration']) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date Needed</span>
                            <span class="detail-value">
                                <i class="bi bi-calendar-event"></i>
                                <?= date('M d, Y', strtotime($req['date_needed'])) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Time</span>
                            <span class="detail-value">
                                <i class="bi bi-clock"></i>
                                <?= date('g:i A', strtotime($req['time_from'])) ?> - <?= date('g:i A', strtotime($req['time_to'])) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Room</span>
                            <span class="detail-value">
                                <i class="bi bi-building"></i>
                                <?= htmlspecialchars($req['room']) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Subject</span>
                            <span class="detail-value">
                                <i class="bi bi-journal-text"></i>
                                <?= htmlspecialchars($req['subject']) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Instructor</span>
                            <span class="detail-value">
                                <i class="bi bi-person-badge"></i>
                                <?= htmlspecialchars($req['faculty_name']) ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Purpose</span>
                            <span class="detail-value" style="grid-column: span 2;">
                                <i class="bi bi-pencil-square"></i>
                                <?= htmlspecialchars(substr($req['purpose'], 0, 80)) ?><?= strlen($req['purpose']) > 80 ? '...' : '' ?>
                            </span>
                        </div>
                    </div>

                    <div class="request-footer">
                        <div class="request-date">
                            <i class="bi bi-calendar-check"></i>
                            Requested on <?= date('F j, Y g:i A', strtotime($req['date_requested'])) ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>No Requests Yet</h3>
                <p>You haven't made any borrow requests yet.</p>
                <a href="borrow_form.php" class="btn-new-request">
                    <i class="bi bi-plus-circle"></i> Create New Request
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.getAttribute('data-filter');
        const cards = document.querySelectorAll('.request-card');
        
        cards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-status') === filter) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease-out';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>