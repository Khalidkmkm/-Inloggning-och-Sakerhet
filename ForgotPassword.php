<?php
require_once('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            // Generera token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24); // 24 timmar
            // Spara i password_resets
            $stmt = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $token, $expires]);
            $reset_link = "http://localhost:8080/ResetPassword.php?token=$token";
            $success = 'A reset link has been generated (for demo, se nedan).';
        } else {
            $error = 'No user found with that email address.';
        }
    }
}

require_once('lib/PageTemplate.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Forgot Password";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>
<p>
<div class="row">
    <div class="col-md-12">
        <div class="newsletter">
            <p>User<strong>&nbsp;FORGOT PASSWORD</strong></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input class="input" type="email" name="email" placeholder="Enter your email" required>
                <br/>
                <br/>
                <button type="submit" class="newsletter-btn">Send reset link</button>
            </form>
            <?php if ($reset_link): ?>
                <div class="alert alert-info">
                    <strong>Reset link (demo):</strong> <a href="<?php echo htmlspecialchars($reset_link); ?>"><?php echo htmlspecialchars($reset_link); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</p> 