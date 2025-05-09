<?php
require_once('config.php');
require_once('lib/PageTemplate.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            // Logga inloggningstillfället
            $ip = $_SERVER['REMOTE_ADDR'];
            $stmt = $conn->prepare("INSERT INTO login_sessions (user_id, ip_address) VALUES (?, ?)");
            $stmt->execute([$user['id'], $ip]);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}

# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Login";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>
<p>
<div class="row">
    <div class="col-md-12">
        <div class="newsletter">
            <p>User<strong>&nbsp;LOGIN</strong></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input class="input" type="email" name="email" placeholder="Enter Your Email" required>
                <br/>
                <br/>
                <input class="input" type="password" name="password" placeholder="Enter Your Password" required>
                <br/>
                <br/>
                <button type="submit" class="newsletter-btn"><i class="fa fa-envelope"></i> Login</button>
            </form>
            <a href="ForgotPassword.php">Forgot password?</a>
            <a href="AccountRegister.php">Don't have an account? Register here</a>
        </div>
    </div>
</div>
</p>