<?php
session_start();
require_once '../config/db.php';
require_once '../includes/captcha.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');

    if (!checkCaptcha($captcha)) {
        $error = "Incorrect CAPTCHA code.";
    } else {
        // Replace with your desired admin credentials or look up in users table
        $admin_user = 'Teyvat';
        $admin_pass = 'I Love Genshin'; // Change this for production!

        if ($username === $admin_user && $password === $admin_pass) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: ../admin/dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// On GET: generate new CAPTCHA
$captcha_code = generateCaptchaString();
?>

<?php require_once '../includes/header.php'; ?>
<div class="container" style="max-width:400px;">
    <form method="post" class="login-form">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <label>Password:
            <input type="password" name="password" required>
        </label>
        <label>CAPTCHA:
            <span class="captcha"><?= htmlspecialchars($captcha_code) ?></span>
            <input type="text" name="captcha" required placeholder="Enter code above">
        </label>
        <button type="submit">Login</button>
    </form>
</div>
