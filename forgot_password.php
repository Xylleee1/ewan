<?php
session_start();
require_once('includes/db.php');
require_once('includes/notifications.php');

$err = "";
$success = "";
$step = isset($_GET['step']) ? $_GET['step'] : 1;

// Step 1: Request reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $err = "Please enter your email address.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, full_name, username FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $user_id, $full_name, $username);
            mysqli_stmt_fetch($stmt);
            
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in session (in production, store in database)
            $_SESSION['reset_token'] = $reset_token;
            $_SESSION['reset_user_id'] = $user_id;
            $_SESSION['reset_expiry'] = $reset_expiry;
            
            // Send email with reset link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/forgot_password.php?step=2&token=" . $reset_token;
            
            $email_subject = "Password Reset Request - CSM System";
            $email_message = "
                <p>You have requested to reset your password.</p>
                <p><strong>Username:</strong> $username</p>
                <p>Click the link below to reset your password (valid for 1 hour):</p>
                <p><a href='$reset_link' class='button'>Reset Password</a></p>
                <p>If you did not request this, please ignore this email.</p>
            ";
            
            send_email_notification($email, $full_name ?: $username, $email_subject, $email_message);
            
            $success = "Password reset instructions have been sent to your email.";
        } else {
            // Don't reveal if email exists (security)
            $success = "If this email exists in our system, you will receive reset instructions.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Step 2: Verify token and reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($new_password) || empty($confirm_password)) {
        $err = "Please fill in all fields.";
    } elseif (strlen($new_password) < 6) {
        $err = "Password must be at least 6 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $err = "Passwords do not match.";
    } elseif (!isset($_SESSION['reset_token']) || $token !== $_SESSION['reset_token']) {
        $err = "Invalid or expired reset token.";
    } elseif (strtotime($_SESSION['reset_expiry']) < time()) {
        $err = "Reset token has expired. Please request a new one.";
    } else {
        $user_id = $_SESSION['reset_user_id'];
        $hashed_password = hash_password($new_password);
        
        $update = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($update, "si", $hashed_password, $user_id);
        
        if (mysqli_stmt_execute($update)) {
            // Clear reset session data
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_expiry']);
            
            add_log($conn, $user_id, "Password Reset", "Password successfully reset via email verification.");
            
            $success = "Password successfully updated. You can now <a href='index.php'>login</a>.";
            $step = 3;
        } else {
            $err = "Failed to update password. Please try again.";
        }
        mysqli_stmt_close($update);
    }
}

// Verify token for step 2
if ($step == 2 && isset($_GET['token'])) {
    $token = $_GET['token'];
    if (!isset($_SESSION['reset_token']) || $token !== $_SESSION['reset_token']) {
        $err = "Invalid or expired reset token.";
        $step = 1;
    } elseif (strtotime($_SESSION['reset_expiry']) < time()) {
        $err = "Reset token has expired. Please request a new one.";
        $step = 1;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password - CSM Borrowing System</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #FF6F00 0%, #FFA040 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
.card { background: #fff; width: 90%; max-width: 500px; padding: 40px 35px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
h3 { text-align: center; background: linear-gradient(135deg, #FF6F00, #FFA040); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; font-size: 26px; font-weight: 700; }
.subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
label { font-weight: 600; display: block; margin-bottom: 8px; color: #333; font-size: 14px; }
input[type="text"], input[type="password"], input[type="email"] { width: 100%; padding: 14px 18px; margin-bottom: 20px; border-radius: 10px; border: 2px solid #e0e0e0; font-size: 15px; transition: border-color 0.3s; }
input:focus { outline: none; border-color: #FF6F00; box-shadow: 0 0 0 3px rgba(255,111,0,0.1); }
button { width: 100%; padding: 14px; background: linear-gradient(135deg, #FF6F00, #FFA040); color: white; border: none; font-weight: 700; border-radius: 10px; cursor: pointer; font-size: 16px; transition: all 0.3s; }
button:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,111,0,0.4); }
.alert { background: #fff3e0; color: #e65100; border: 1px solid #ffb74d; border-left: 4px solid #ff6f00; padding: 14px; text-align: center; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
.success { background: #e6ffed; color: #1a7f37; border: 1px solid #3ddc97; border-left: 4px solid #28a745; padding: 14px; text-align: center; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
a { color: #FF6F00; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }
.back-link { text-align: center; margin-top: 20px; }
.info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 13px; color: #1565c0; }
</style>
</head>
<body>
<div class="card">
    <h3><i class="bi bi-key-fill"></i> Password Reset</h3>
    <p class="subtitle">Recover your account access</p>

    <?php if ($err): ?>
        <div class="alert"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($step == 1): ?>
        <div class="info-box">
            <i class="bi bi-info-circle"></i> Enter your email address and we'll send you instructions to reset your password.
        </div>
        
        <form method="POST" action="">
            <label><i class="bi bi-envelope-fill"></i> Email Address</label>
            <input type="email" name="email" placeholder="Enter your registered email" required autofocus>
            
            <button type="submit" name="request_reset"><i class="bi bi-send"></i> Send Reset Link</button>
        </form>
    
    <?php elseif ($step == 2): ?>
        <div class="info-box">
            <i class="bi bi-shield-check"></i> Enter your new password below.
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            
            <label><i class="bi bi-lock-fill"></i> New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password (min 6 characters)" required minlength="6">

            <label><i class="bi bi-lock-fill"></i> Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>

            <button type="submit" name="reset_password"><i class="bi bi-arrow-repeat"></i> Reset Password</button>
        </form>
    
    <?php elseif ($step == 3): ?>
        <div class="success">
            <i class="bi bi-check-circle-fill"></i> Your password has been successfully reset!<br><br>
            <a href="index.php" style="font-size: 16px;">Click here to login</a>
        </div>
    <?php endif; ?>

    <div class="back-link">
        <a href="index.php"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </div>
</div>
</body>
</html>