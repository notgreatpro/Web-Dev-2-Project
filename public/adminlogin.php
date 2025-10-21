<?php
session_start();
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');

    // Check CAPTCHA
    if (empty($captcha) || strtolower($captcha) !== strtolower($_SESSION['captcha_code'] ?? '')) {
        $error = "Incorrect CAPTCHA code.";
    } else {
        $admin_user = 'Teyvat';
        $admin_pass = 'I Love Genshin'; 

        if ($username === $admin_user && $password === $admin_pass) {
            $_SESSION['admin_logged_in'] = true;
            unset($_SESSION['captcha_code']); // Prevent reuse after login
            header('Location: ../admin/dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// On GET: generate new CAPTCHA
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error) {
    // Regenerate CAPTCHA for each GET or after error
    $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    $_SESSION['captcha_code'] = $code;
}
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
        <label style="font-weight:bold; color:#23233b; font-size:1.15em;">CAPTCHA:<br>
            <img src="../includes/captcha.php" alt="CAPTCHA" style="margin-top:6px; margin-bottom:6px;" id="captcha-img">
            <input 
                type="text" 
                name="captcha" 
                required 
                placeholder="Enter code above" 
            >
        </label>
        <button type="submit">Login</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>