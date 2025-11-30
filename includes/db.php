<?php
/**
 * CSM Apparatus System - Enhanced Database Helper
 * Secure database connection and utility functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'csm_apparatus_system');

// Create secure connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("System temporarily unavailable. Please try again later.");
}

// Set charset to utf8mb4 for security
mysqli_set_charset($conn, 'utf8mb4');

// Set timezone
date_default_timezone_set('Asia/Manila');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

/**
 * Session Security & Timeout Management
 */
function init_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        // Session security settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        session_start();
        
        // Session timeout (1 hour)
        $timeout = 3600;
        
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
            session_unset();
            session_destroy();
            header('Location: index.php?timeout=1');
            exit();
        }
        
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
        
        // Prevent session fixation
        if (!isset($_SESSION['USER_AGENT'])) {
            $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        } else if ($_SESSION['USER_AGENT'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            session_unset();
            session_destroy();
            header('Location: index.php?security=1');
            exit();
        }
    }
}

/**
 * Password hashing functions - FIXED VERSION
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password with automatic rehashing for legacy passwords
 * Returns: ['valid' => bool, 'needs_rehash' => bool]
 */
function verify_password($password, $hash) {
    // Check if hash is bcrypt (starts with $2y$ or $2a$ or $2b$)
    if (preg_match('/^\$2[ayb]\$/', $hash)) {
        return [
            'valid' => password_verify($password, $hash),
            'needs_rehash' => password_needs_rehash($hash, PASSWORD_DEFAULT)
        ];
    }
    
    // Check for legacy MD5 (32 character hex string)
    if (strlen($hash) === 32 && ctype_xdigit($hash)) {
        $is_valid = (md5($password) === $hash);
        return [
            'valid' => $is_valid,
            'needs_rehash' => $is_valid // Always rehash MD5 passwords
        ];
    }
    
    // Check for plain text (legacy)
    $is_valid = ($password === $hash);
    return [
        'valid' => $is_valid,
        'needs_rehash' => $is_valid // Always rehash plain text passwords
    ];
}

/**
 * Rehash password if needed - call this after successful login
 */
function rehash_user_password($conn, $user_id, $plain_password) {
    $user_id = intval($user_id);
    $new_hash = hash_password($plain_password);
    
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $new_hash, $user_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

/**
 * Add activity log entry (SECURE VERSION)
 */
function add_log($conn, $user_id, $action, $description = '') {
    $stmt = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_id = intval($user_id);
    mysqli_stmt_bind_param($stmt, 'isss', $user_id, $action, $description, $ip_address);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * Check apparatus availability (SECURE VERSION)
 */
function check_apparatus_availability($conn, $apparatus_id, $quantity) {
    $apparatus_id = intval($apparatus_id);
    $stmt = mysqli_prepare($conn, "SELECT name, quantity FROM apparatus WHERE apparatus_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $apparatus_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return [
            'available' => $row['quantity'] >= $quantity,
            'current_stock' => $row['quantity'],
            'name' => $row['name']
        ];
    }
    
    mysqli_stmt_close($stmt);
    return ['available' => false, 'current_stock' => 0, 'name' => 'Unknown'];
}

/**
 * Calculate penalty for late return
 */
function calculate_late_penalty($due_date, $return_date, $penalty_per_day = 50.00) {
    $due = new DateTime($due_date);
    $returned = new DateTime($return_date);
    
    if ($returned > $due) {
        $days_late = $returned->diff($due)->days;
        return $days_late * $penalty_per_day;
    }
    
    return 0;
}

/**
 * Get user statistics (SECURE VERSION)
 */
function get_user_stats($conn, $user_id, $role) {
    $stats = [];
    $user_id = intval($user_id);
    
    if ($role === 'student') {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE student_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['total_requests'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);
        
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE student_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['pending'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);
        
        $stmt = mysqli_prepare($conn, "
            SELECT COALESCE(SUM(p.amount), 0) as total 
            FROM penalties p
            LEFT JOIN transactions t ON p.transaction_id = t.transaction_id
            LEFT JOIN borrow_requests br ON t.request_id = br.request_id
            WHERE br.student_id = ? AND p.status != 'paid'
        ");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['penalties'] = mysqli_fetch_assoc($result)['total'] ?? 0;
        mysqli_stmt_close($stmt);
        
    } elseif ($role === 'faculty') {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE faculty_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['pending_approvals'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);
        
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE faculty_id = ? AND status = 'approved'");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['approved'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);
        
    } elseif ($role === 'admin' || $role === 'assistant') {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM apparatus");
        $stats['total_apparatus'] = mysqli_fetch_assoc($result)['total'];
        
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE status = 'pending'");
        $stats['pending_requests'] = mysqli_fetch_assoc($result)['total'];
        
        $result = mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM penalties WHERE status != 'paid'");
        $stats['total_penalties'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    }
    
    return $stats;
}

/**
 * Sanitize input (prevent XSS)
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize for SQL (prevent injection)
 */
function sanitize_sql($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Format date for display
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date)) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Check if user has permission
 */
function has_permission($user_role, $allowed_roles) {
    return in_array($user_role, (array)$allowed_roles);
}

/**
 * Get apparatus categories (SECURE VERSION)
 */
function get_categories($conn) {
    $categories = [];
    $result = mysqli_query($conn, "SELECT DISTINCT category FROM apparatus ORDER BY category");
    
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['category'];
    }
    
    return $categories;
}

/**
 * Get recent activities (SECURE VERSION)
 */
function get_recent_activities($conn, $limit = 10) {
    $limit = intval($limit);
    $stmt = mysqli_prepare($conn, "
        SELECT l.*, u.full_name, u.role, u.email
        FROM activity_logs l
        LEFT JOIN users u ON l.user_id = u.user_id
        ORDER BY l.timestamp DESC
        LIMIT ?
    ");
    mysqli_stmt_bind_param($stmt, 'i', $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Check for overdue returns
 */
function get_overdue_returns($conn) {
    $today = date('Y-m-d');
    $query = "
        SELECT br.*, 
               COALESCE(s.full_name, s.username) AS student_name, 
               s.email,
               a.name AS apparatus_name,
               DATEDIFF(CURDATE(), br.date_needed) as days_overdue
        FROM borrow_requests br
        LEFT JOIN users s ON br.student_id = s.user_id
        LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        WHERE br.status IN ('approved', 'released')
        AND br.date_needed < ?
        ORDER BY br.date_needed ASC
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $today);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Validate WMSU email
 */
function is_wmsu_email($email) {
    return stripos($email, '@wmsu.edu.ph') !== false;
}

/**
 * Generate unique transaction code
 */
function generate_transaction_code() {
    return 'TXN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Get borrowing history for a student (SECURE VERSION)
 */
function get_borrowing_history($conn, $student_id) {
    $student_id = intval($student_id);
    $stmt = mysqli_prepare($conn, "
        SELECT br.*, a.name AS apparatus_name, a.category,
               COALESCE(f.full_name, f.username) AS faculty_name,
               t.date_borrowed, t.date_returned
        FROM borrow_requests br
        LEFT JOIN apparatus a ON br.apparatus_id = a.apparatus_id
        LEFT JOIN users f ON br.faculty_id = f.user_id
        LEFT JOIN transactions t ON br.request_id = t.request_id
        WHERE br.student_id = ?
        ORDER BY br.date_requested DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Check if student has pending penalties (SECURE VERSION)
 */
function check_student_penalties($conn, $student_id) {
    $student_id = intval($student_id);
    $stmt = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(p.amount), 0) as total_penalties
        FROM penalties p
        LEFT JOIN transactions t ON p.transaction_id = t.transaction_id
        LEFT JOIN borrow_requests br ON t.request_id = br.request_id
        WHERE br.student_id = ?
        AND p.status != 'paid'
    ");
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $total = $row['total_penalties'] ?? 0;
    mysqli_stmt_close($stmt);
    
    return [
        'has_penalties' => $total > 0,
        'total_amount' => $total
    ];
}

/**
 * Update apparatus quantity (SECURE VERSION)
 */
function update_apparatus_quantity($conn, $apparatus_id, $quantity_change) {
    $apparatus_id = intval($apparatus_id);
    $quantity_change = intval($quantity_change);
    $stmt = mysqli_prepare($conn, "
        UPDATE apparatus 
        SET quantity = GREATEST(0, quantity + ?) 
        WHERE apparatus_id = ?
    ");
    mysqli_stmt_bind_param($stmt, 'ii', $quantity_change, $apparatus_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * Get low stock items
 */
function get_low_stock_items($conn, $threshold = 5) {
    $threshold = intval($threshold);
    $stmt = mysqli_prepare($conn, "
        SELECT apparatus_id, name, category, quantity
        FROM apparatus
        WHERE quantity <= ? AND quantity > 0
        ORDER BY quantity ASC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $threshold);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Archive old records
 */
function archive_old_records($conn, $days_old = 365) {
    $days_old = intval($days_old);
    $cutoff_date = date('Y-m-d', strtotime("-$days_old days"));
    
    $stmt = mysqli_prepare($conn, "
        UPDATE borrow_requests 
        SET archived = 1 
        WHERE status IN ('returned', 'rejected') 
        AND date_requested < ?
    ");
    mysqli_stmt_bind_param($stmt, 's', $cutoff_date);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    
    return $affected;
}

/**
 * Get system setting
 */
function get_setting($conn, $key, $default = null) {
    $stmt = mysqli_prepare($conn, "SELECT setting_value FROM system_settings WHERE setting_key = ?");
    mysqli_stmt_bind_param($stmt, 's', $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return $row['setting_value'];
    }
    
    mysqli_stmt_close($stmt);
    return $default;
}

/**
 * Update system setting
 */
function update_setting($conn, $key, $value) {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO system_settings (setting_key, setting_value) 
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = ?
    ");
    mysqli_stmt_bind_param($stmt, 'sss', $key, $value, $value);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Initialize secure session
init_secure_session();