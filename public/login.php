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
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Username:<br>
            <input type="text" name="username" required>
        </label><br><br>
        <label>Password:<br>
            <input type="password" name="password" required>
        </label><br><br>
        <label>CAPTCHA: <b><?= htmlspecialchars($captcha_code) ?></b><br>
            <input type="text" name="captcha" required placeholder="Enter code above">
        </label><br><br>
        <button type="submit">Login</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>