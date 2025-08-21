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
        $admin_user = 'Teyvat';
        $admin_pass = 'I Love Genshin'; 

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
$captcha_code = generateCaptchaString(6);
$_SESSION['captcha_code'] = $captcha_code;
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
        <label style="font-weight:bold; color:#23233b; font-size:1.15em;">CAPTCHA:
            <span class="captcha" style="font-family:Hoyo Font,serif; font-size:1.35em; letter-spacing:2px; color:#23233b; background:transparent; font-weight:800;"><?= htmlspecialchars($captcha_code) ?></span>
            <input 
                type="text" 
                name="captcha" 
                required 
                placeholder="Enter code above" 
                style="margin-top:0.6em; font-family:Hoyo Font,serif; font-size:1.15em; letter-spacing:1px; border:2px solid #ffe066; background:#fff; border-radius:8px; padding:12px; width:100%; box-sizing:border-box; color:#23233b; font-weight:600;"
            >
        </label>
        <button type="submit">Login</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>