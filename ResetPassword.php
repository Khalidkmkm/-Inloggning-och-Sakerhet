<?php
require_once('config.php');
require_once('lib/PageTemplate.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
$show_form = false;

$token = $_GET['token'] ?? '';

if ($token) {
    // Hämta återställningsposten
    $stmt = $conn->prepare('SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0');
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    if ($reset) {
        $show_form = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            if (empty($password) || empty($confirm_password)) {
                $error = 'Please fill in all fields.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters long.';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } else {
                // Uppdatera lösenordet
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$hashed_password, $reset['user_id']]);
                // Markera token som använd
                $stmt = $conn->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
                $stmt->execute([$reset['id']]);
                $success = 'Your password has been reset. You can now <a href="AccountLogin.php">login</a>.';
                $show_form = false;
            }
        }
    } else {
        $error = 'Invalid or expired reset link.';
    }
} else {
    $error = 'No reset token provided.';
}

# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Reset Password";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>
<p>
<div class="row">
    <div class="col-md-12">
        <div class="newsletter">
            <p>User<strong>&nbsp;RESET PASSWORD</strong></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($show_form): ?>
            <form method="POST" action="">
                <input class="input" type="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
                <br/>
                <br/>
                <input class="input" type="password" name="confirm_password" placeholder="Confirm new password" required autocomplete="new-password">
                <br/>
                <br/>
                <button type="submit" class="newsletter-btn">Reset Password</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</p> 