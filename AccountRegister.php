<?php
require_once('config.php');
require_once('lib/PageTemplate.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if (empty($email) || empty($name) || empty($password) || empty($confirm_password) || empty($street) || empty($postal_code) || empty($city)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!preg_match('/^[0-9 ]+$/', $postal_code)) {
        $error = 'Postal code must only contain numbers and spaces';
    } elseif (!preg_match('/^[a-zA-ZåäöÅÄÖ0-9 .\-]+$/u', $street)) {
        $error = 'Street address contains invalid characters';
    } elseif (!preg_match('/^[a-zA-ZåäöÅÄÖ .\-]+$/u', $city)) {
        $error = 'City contains invalid characters';
    } elseif (!preg_match('/^[a-zA-ZåäöÅÄÖ .\-]+$/u', $name)) {
        $error = 'Name contains invalid characters';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            // Hash password and create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, name, password, street, postal_code, city) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$email, $name, $hashed_password, $street, $postal_code, $city])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Register";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>
<p>
<div class="row">
    <div class="col-md-12">
        <div class="newsletter">
            <p>User<strong>&nbsp;REGISTRATION</strong></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="" autocomplete="off">
                <input class="input" type="email" name="email" placeholder="Enter Your Email" required autocomplete="off">
                <br/>
                <br/>
                <input class="input" type="text" name="name" placeholder="Enter Your Name" required>
                <br/>
                <br/>
                <input class="input" type="password" name="password" placeholder="Enter Your Password" required autocomplete="new-password">
                <br/>
                <br/>
                <input class="input" type="password" name="confirm_password" placeholder="Confirm Your Password" required autocomplete="new-password">
                <br/>
                <br/>
                <input class="input" type="text" name="street" placeholder="Street Address" required>
                <br/>
                <br/>
                <input class="input" type="text" name="postal_code" placeholder="Postal Code" required>
                <br/>
                <br/>
                <input class="input" type="text" name="city" placeholder="City" required>
                <br/>
                <br/>
                <button type="submit" class="newsletter-btn"><i class="fa fa-user-plus"></i> Register</button>
            </form>
            <a href="AccountLogin.php">Already have an account? Login here</a>
        </div>
    </div>
</div>
</p>