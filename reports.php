<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Access control
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'assistant'])) {
    session_start();
    require_once __DIR__ . '/includes/header.php';
    echo "<div class='alert alert-danger text-center mt-5'>Access denied.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit();
}

// === HANDLE EXPORT (BEFORE ANY OUTPUT) ===
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    $date_from = $_GET['date_from'] ?? date('Y-m-01');
    $date_to = $_GET['date_to'] ?? date('Y-m-d');
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $export_type . '_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    if ($export_type === 'summary') {
        fputcsv($output, ['CSM Apparatus System - Summary Report']);
        fputcsv($output, ['Generated: ' . date('M d, Y h:i A')]);
        fputcsv($output, ['Period: ' . date('M d, Y', strtotime($date_from)) . ' to ' . date('M d, Y', strtotime($date_to))]);
        fputcsv($output, []);
        
        fputcsv($output, ['Metric', 'Value']);
        
        $stats = [
            'Total Requests' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM borrow_requests WHERE date_requested BETWEEN '$date_from' AND '$date_to'"))['c'],
            'Approved' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM borrow_requests WHERE status='approved' AND date_requested BETWEEN '$date_from' AND '$date_to'"))['c'],
            'Rejected' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM borrow_requests WHERE status='rejected' AND date_requested BETWEEN '$date_from' AND '$date_to'"))['c'],
            'Pending' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM borrow_requests WHERE status='pending' AND date_requested BETWEEN '$date_from' AND '$date_to'"))['c'],
            'Total Penalties' => '₱' . number_format(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as c FROM penalties WHERE date_imposed BETWEEN '$date_from' AND '$date_to'"))['c'], 2),
        ];
        
        foreach ($stats as $metric => $value) {
            fputcsv($output, [$metric, $value]);
        }
        
    } elseif ($export_type === 'requests') {
        fputcsv($output, ['Request ID', 'Student', 'Apparatus', 'Quantity', 'Faculty', 'Date Needed', 'Date Requested', 'Status']);
        
        $result = mysqli_query($conn, "
            SELECT br.request_id, 
                   COALESCE(s.full_name, s.username) as student_name,
                   a.name as apparatus_name,
                   br.quantity,
                   COALESCE(f.full_name, f.username) as faculty_name,
                   br.date_needed,
                   br.date_requested,
                   br.status
            FROM borrow_requests br
            LEFT JOIN users s ON br.student_id = s.user_id
            LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
            LEFT JOIN users f ON br.faculty_id = f.user_id
            WHERE br.date_requested BETWEEN '$date_from' AND '$date_to'
            ORDER BY br.date_requested DESC
        ");
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['request_id'],
                $row['student_name'],
                $row['apparatus_name'],
                $row['quantity'],
                $row['faculty_name'],
                $row['date_needed'],
                $row['date_requested'],
                $row['status']
            ]);
        }
        
    } elseif ($export_type === 'penalties') {
        fputcsv($output, ['Penalty ID', 'Transaction ID', 'Student', 'Reason', 'Amount', 'Date Imposed', 'Status']);
        
        $result = mysqli_query($conn, "
            SELECT p.penalty_id,
                   p.transaction_id,
                   COALESCE(s.full_name, s.username) as student_name,
                   p.reason,
                   p.amount,
                   p.date_imposed,
                   COALESCE(p.status, 'unpaid') as status
            FROM penalties p
            LEFT JOIN transactions t ON p.transaction_id = t.transaction_id
            LEFT JOIN borrow_requests br ON t.request_id = br.request_id
            LEFT JOIN users s ON br.student_id = s.user_id
            WHERE p.date_imposed BETWEEN '$date_from' AND '$date_to'
            ORDER BY p.date_imposed DESC
        ");
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['penalty_id'],
                $row['transaction_id'],
                $row['student_name'],
                $row['reason'],
                $row['amount'],
                $row['date_imposed'],
                $row['status']
            ]);
        }
    }
    
    fclose($output);
    exit();
}

// Now include header after export check
require_once __DIR__ . '/includes/header.php';

// Include Chart.js CDN
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

// === DATE FILTERS ===
$earliest_date = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(date_requested) as earliest FROM borrow_requests"))['earliest'] ?? date('Y-01-01');
$date_from = $_GET['date_from'] ?? $earliest_date;
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// === REPORT STATISTICS ===
$total_requests = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM borrow_requests 
    WHERE date_requested BETWEEN '$date_from' AND '$date_to'
"))['count'];

$approved_requests = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM borrow_requests 
    WHERE status='approved' AND date_requested BETWEEN '$date_from' AND '$date_to'
"))['count'];

$rejected_requests = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM borrow_requests 
    WHERE status='rejected' AND date_requested BETWEEN '$date_from' AND '$date_to'
"))['count'];

$pending_requests = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM borrow_requests 
    WHERE status='pending' AND date_requested BETWEEN '$date_from' AND '$date_to'
"))['count'];

$total_penalties = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(amount), 0) as total FROM penalties 
    WHERE date_imposed BETWEEN '$date_from' AND '$date_to'
"))['total'] ?? 0;

$unpaid_penalties = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(amount), 0) as total FROM penalties 
    WHERE status != 'paid' AND date_imposed BETWEEN '$date_from' AND '$date_to'
"))['total'] ?? 0;

$approval_rate = $total_requests > 0 ? round(($approved_requests / $total_requests) * 100, 1) : 0;

$total_apparatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM apparatus"))['count'];
$available_apparatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM apparatus WHERE status='Available' AND quantity > 0"))['count'];
$active_borrowings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_requests WHERE status IN ('approved', 'released')"))['count'];
$overdue_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_requests WHERE status IN ('approved', 'released') AND date_needed < CURDATE()"))['count'];
$low_stock_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM apparatus WHERE quantity <= 5 AND quantity > 0"))['count'];
$released_returned = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_requests WHERE status IN ('released', 'returned')"))['count'];

// Most borrowed apparatus
$most_borrowed = mysqli_query($conn, "
    SELECT a.name, a.category, COUNT(br.request_id) as borrow_count
    FROM borrow_requests br
    JOIN apparatus a ON br.apparatus_id = a.apparatus_id
    WHERE br.date_requested BETWEEN '$date_from' AND '$date_to'
    GROUP BY br.apparatus_id
    ORDER BY borrow_count DESC
    LIMIT 10
");

// Active students
$active_students = mysqli_query($conn, "
    SELECT COALESCE(u.full_name, u.username) as student_name, 
           u.email, 
           COUNT(br.request_id) as request_count
    FROM borrow_requests br
    JOIN users u ON br.student_id = u.user_id
    WHERE br.date_requested BETWEEN '$date_from' AND '$date_to'
    GROUP BY br.student_id
    ORDER BY request_count DESC
    LIMIT 10
");

// Low stock items
$low_stock = mysqli_query($conn, "
    SELECT apparatus_id, name, category, quantity
    FROM apparatus
    WHERE quantity <= 5 AND quantity > 0
    ORDER BY quantity ASC
    LIMIT 10
");

// Overdue items
$overdue = mysqli_query($conn, "
    SELECT br.request_id, br.date_needed, 
           a.name as apparatus_name,
           COALESCE(u.full_name, u.username) as student_name, 
           u.email,
           DATEDIFF(CURDATE(), br.date_needed) as days_overdue
    FROM borrow_requests br
    JOIN apparatus a ON br.apparatus_id = a.apparatus_id
    JOIN users u ON br.student_id = u.user_id
    WHERE br.status IN ('approved', 'released')
    AND br.date_needed < CURDATE()
    ORDER BY br.date_needed ASC
");

?>

<style>
:root {
    --primary: #ff6b35;
    --primary-dark: #cc5729;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
}

body {
    background: linear-gradient(135deg, #cf6e00ff 0%, #ffbf00ff 100%);
    min-height: 100vh;
}

.container-custom {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

/* ========== PAGE HEADER ========== */

.page-header {
    background: white;
    padding: 24px 32px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    margin-bottom: 32px;
}
.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header h1 i {
    font-size: 36px;
}

.page-subtitle {
    opacity: 0.9;
    font-size: 14px;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 12px;
}

.btn-export, .btn-print {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: var(--primary);
    padding: 12px 24px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-export:hover, .btn-print:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    color: var(--primary-dark);
}

/* ========== DATE FILTER ========== */
.date-filter {
    background: white;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 16px;
    align-items: end;
}

.filter-grid label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--gray-700);
    font-size: 14px;
}

.filter-grid input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter-grid input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.btn-filter {
    background: linear-gradient(135deg, #ff6b35 0%, #cc5729 100%);
    color: white;
    padding: 12px 32px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255,107,53,0.4);
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,107,53,0.5);
}

/* ========== STATS CARDS ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-card.success::before { background: var(--success); }
.stat-card.danger::before { background: var(--danger); }
.stat-card.warning::before { background: var(--warning); }
.stat-card.info::before { background: var(--info); }

.stat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-card.success .stat-icon { background: #e8f5e9; color: var(--success); }
.stat-card.danger .stat-icon { background: #ffebee; color: var(--danger); }
.stat-card.warning .stat-icon { background: #fff8e1; color: var(--warning); }
.stat-card.info .stat-icon { background: #e3f2fd; color: var(--info); }

.stat-label {
    font-size: 14px;
    color: var(--gray-700);
    font-weight: 500;
    margin: 0;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

/* ========== CHARTS ========== */
.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.chart-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.chart-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-100);
}

.chart-header i {
    font-size: 24px;
    color: var(--primary);
}

.chart-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
}

.chart-container {
    position: relative;
    height: 350px;
}

/* ========== TABLES ========== */
.report-section {
    background: white;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-100);
}

.section-header i {
    font-size: 24px;
    color: var(--primary);
}

.section-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
}

.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead {
    background: linear-gradient(135deg, #ff7300ff 0%, #ff9500ff 100%);
}

.table th {
    padding: 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table th:first-child {
    border-radius: 8px 0 0 0;
}

.table th:last-child {
    border-radius: 0 8px 0 0;
}

.table td {
    padding: 16px;
    font-size: 14px;
    color: var(--gray-800);
    border-bottom: 1px solid var(--gray-200);
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background: var(--gray-50);
    transform: scale(1.01);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.table tbody tr:last-child td:first-child {
    border-radius: 0 0 0 8px;
}

.table tbody tr:last-child td:last-child {
    border-radius: 0 0 8px 0;
}

/* ========== BADGES ========== */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.badge-danger { background: #ffebee; color: #c62828; }
.badge-warning { background: #fff8e1; color: #f57c00; }
.badge-success { background: #e8f5e9; color: #2e7d32; }
.badge-info { background: #e3f2fd; color: #1565c0; }

/* ========== DROPDOWN MENU ========== */
.dropdown-container {
    position: relative;
    display: inline-block;
}

.export-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    background: white;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border-radius: 12px;
    z-index: 10;
    min-width: 200px;
    overflow: hidden;
}

.export-menu.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.export-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: var(--gray-700);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.export-menu a:hover {
    background: var(--gray-100);
    color: var(--primary);
}

.export-menu a i {
    font-size: 16px;
}

/* ========== EMPTY STATE ========== */
.empty-state {
    text-align: center;
    padding: 48px 20px;
    color: var(--gray-700);
}

.empty-state i {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
}

.empty-state p {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

/* ========== PRINT STYLES ========== */
@media print {
    body {
        background: white !important;
    }
    
    .navbar, .btn-filter, .btn-export, .btn-print, .date-filter, .action-buttons {
        display: none !important;
    }
    
    .page-header {
        background: white !important;
        color: black !important;
        border: 2px solid #000;
        page-break-after: avoid;
    }
    
    .page-header::before {
        display: none;
    }
    
    .stat-card, .chart-card, .report-section {
        page-break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
    
    .table thead {
        background: #f0f0f0 !important;
        color: black !important;
    }
    
    .charts-section {
        page-break-before: always;
    }
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
    .page-header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .action-buttons {
        width: 100%;
    }
    
    .btn-export, .btn-print {
        flex: 1;
        justify-content: center;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container-custom">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div>
                <h1><i class="bi bi-bar-chart-fill"></i> System Reports & Analytics</h1>
                <p class="page-subtitle">
                    Period: <?= date('M d, Y', strtotime($date_from)) ?> - <?= date('M d, Y', strtotime($date_to)) ?>
                </p>
            </div>
            <div class="action-buttons">
                <button class="btn-print" onclick="window.print()">
                    <i class="bi bi-printer-fill"></i> Print Report
                </button>
                <div class="dropdown-container">
                    <button class="btn-export" onclick="toggleExportMenu()">
                        <i class="bi bi-download"></i> Export Data
                    </button>
                    <div id="exportMenu" class="export-menu">
                        <a href="?export=summary&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">
                            <i class="bi bi-file-earmark-text-fill"></i> Summary CSV
                        </a>
                        <a href="?export=requests&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">
                            <i class="bi bi-journal-text"></i> Requests CSV
                        </a>
                        <a href="?export=penalties&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">
                            <i class="bi bi-cash-coin"></i> Penalties CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <form method="GET" class="date-filter">
        <div class="filter-grid">
            <div>
                <label><i class="bi bi-calendar-event"></i> From Date</label>
                <input type="date" name="date_from" value="<?= $date_from ?>" required>
            </div>
            <div>
                <label><i class="bi bi-calendar-check"></i> To Date</label>
                <input type="date" name="date_to" value="<?= $date_to ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel-fill"></i> Generate Report
                </button>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <p class="stat-label">Total Requests</p>
        </div>
        <p class="stat-value"><?= number_format($total_requests) ?></p>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <p class="stat-label">Approved Requests</p>
        </div>
        <p class="stat-value"><?= number_format($approved_requests) ?></p>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-clock-fill"></i>
            </div>
            <p class="stat-label">Pending Requests</p>
        </div>
        <p class="stat-value"><?= number_format($pending_requests) ?></p>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <p class="stat-label">Rejected Requests</p>
        </div>
        <p class="stat-value"><?= number_format($rejected_requests) ?></p>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-percent"></i>
            </div>
            <p class="stat-label">Approval Rate</p>
        </div>
        <p class="stat-value"><?= $approval_rate ?>%</p>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <p class="stat-label">Unpaid Penalties</p>
        </div>
        <p class="stat-value">₱<?= number_format($unpaid_penalties, 2) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <p class="stat-label">Available Apparatus</p>
        </div>
        <p class="stat-value"><?= number_format($available_apparatus) ?></p>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <p class="stat-label">Active Borrowings</p>
        </div>
        <p class="stat-value"><?= number_format($active_borrowings) ?></p>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <p class="stat-label">Total Penalties</p>
        </div>
        <p class="stat-value">₱<?= number_format($total_penalties, 2) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <p class="stat-label">Total Apparatus</p>
        </div>
        <p class="stat-value"><?= number_format($total_apparatus) ?></p>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-alarm-fill"></i>
            </div>
            <p class="stat-label">Overdue Items</p>
        </div>
        <p class="stat-value"><?= number_format($overdue_items) ?></p>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <p class="stat-label">Low Stock Items</p>
        </div>
        <p class="stat-value"><?= number_format($low_stock_count) ?></p>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="bi bi-check2-all"></i>
            </div>
            <p class="stat-label">Released/Returned</p>
        </div>
        <p class="stat-value"><?= number_format($released_returned) ?></p>
    </div>
</div>

    <!-- Charts Section -->
    <div class="charts-section">
        <!-- Request Status Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="bi bi-pie-chart-fill"></i>
                <h3>Request Status Distribution</h3>
            </div>
            <div class="chart-container">
                <canvas id="requestStatusChart"></canvas>
            </div>
        </div>

        <!-- Most Borrowed Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="bi bi-bar-chart-fill"></i>
                <h3>Top 10 Most Borrowed Apparatus</h3>
            </div>
            <div class="chart-container">
                <canvas id="mostBorrowedChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Most Borrowed Apparatus Table -->
    <div class="report-section">
        <div class="section-header">
            <i class="bi bi-trophy-fill"></i>
            <h3>Most Borrowed Apparatus</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Apparatus Name</th>
                        <th>Category</th>
                        <th>Times Borrowed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($most_borrowed) > 0): ?>
                        <?php 
                        $rank = 1;
                        mysqli_data_seek($most_borrowed, 0);
                        while ($row = mysqli_fetch_assoc($most_borrowed)): 
                        ?>
                        <tr>
                            <td><strong>#<?= $rank++ ?></strong></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td><strong><?= $row['borrow_count'] ?> times</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No borrowing data available for selected period</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Students Table -->
    <div class="report-section">
        <div class="section-header">
            <i class="bi bi-people-fill"></i>
            <h3>Most Active Students</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Total Requests</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($active_students) > 0): ?>
                        <?php 
                        $rank = 1;
                        while ($row = mysqli_fetch_assoc($active_students)): 
                        ?>
                        <tr>
                            <td><strong>#<?= $rank++ ?></strong></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><strong><?= $row['request_count'] ?> requests</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No student activity data available for selected period</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert Table -->
    <div class="report-section">
        <div class="section-header">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h3>Low Stock Alert</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Apparatus Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($low_stock) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($low_stock)): ?>
                        <tr>
                            <td><strong>#<?= $row['apparatus_id'] ?></strong></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td>
                                <span class="badge <?= $row['quantity'] <= 2 ? 'badge-danger' : 'badge-warning' ?>">
                                    <?= $row['quantity'] ?> remaining
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-check-circle"></i>
                                    <p>All items have sufficient stock</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overdue Items Table -->
    <div class="report-section">
        <div class="section-header">
            <i class="bi bi-clock-history"></i>
            <h3>Overdue Returns</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Student</th>
                        <th>Apparatus</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($overdue) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($overdue)): ?>
                        <tr>
                            <td><strong>#<?= $row['request_id'] ?></strong></td>
                            <td>
                                <?= htmlspecialchars($row['student_name']) ?><br>
                                <small style="color: var(--gray-700);"><?= htmlspecialchars($row['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['apparatus_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['date_needed'])) ?></td>
                            <td>
                                <span class="badge badge-danger">
                                    <i class="bi bi-alarm"></i> <?= $row['days_overdue'] ?> days
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-check-circle"></i>
                                    <p>No overdue items - All returns are on time!</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleExportMenu() {
    document.getElementById('exportMenu').classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.btn-export')) {
        const dropdowns = document.getElementsByClassName('export-menu');
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].classList.remove('show');
        }
    }
}

// Chart.js initialization
document.addEventListener('DOMContentLoaded', function() {
    // Request Status Pie Chart
    const requestStatusCtx = document.getElementById('requestStatusChart').getContext('2d');
    const requestStatusChart = new Chart(requestStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [<?= $approved_requests ?>, <?= $pending_requests ?>, <?= $rejected_requests ?>],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Most Borrowed Apparatus Bar Chart
    const mostBorrowedCtx = document.getElementById('mostBorrowedChart').getContext('2d');
    const apparatusNames = [];
    const borrowCounts = [];

    <?php
    if (mysqli_num_rows($most_borrowed) > 0) {
        mysqli_data_seek($most_borrowed, 0);
        while ($row = mysqli_fetch_assoc($most_borrowed)) {
            echo "apparatusNames.push('" . addslashes($row['name']) . "');\n";
            echo "borrowCounts.push(" . $row['borrow_count'] . ");\n";
        }
    }
    ?>

    const mostBorrowedChart = new Chart(mostBorrowedCtx, {
        type: 'bar',
        data: {
            labels: apparatusNames,
            datasets: [{
                label: 'Times Borrowed',
                data: borrowCounts,
                backgroundColor: 'rgba(255, 115, 0, 0.8)',
                borderColor: 'rgba(255, 157, 0, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Times Borrowed: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
