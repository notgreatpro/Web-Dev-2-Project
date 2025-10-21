<?php
session_start();

// Adjust paths relative to this file (public/)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$error = '';
// Handle POST (login attempt)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha_input = trim($_POST['captcha'] ?? '');

    // CAPTCHA validation (case-insensitive)
    if (empty($captcha_input) || strtolower($captcha_input) !== strtolower($_SESSION['captcha_code'] ?? '')) {
        $error = "Incorrect CAPTCHA code.";
    } else {
        // Check username/password against users table
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_logged_in'] = true;
            // prevent reuse of captcha after success
            unset($_SESSION['captcha_code']);
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// On GET or when there's an error, regenerate the CAPTCHA code so the image updates only then
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error) {
    // Create a 6-character captcha code (no ambiguous chars)
    $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    $_SESSION['captcha_code'] = $code;
}
?>

<div class="user-login-container">
    <form method="post" class="login-form" style="margin:auto;">
        <h2>User Login</h2>

        <?php if ($error): ?>
            <div class="error" style="margin-bottom:12px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label style="display:block; margin-bottom:10px;">
            Username:
            <input type="text" name="username" required style="width:100%; padding:10px; border-radius:8px; border:1.5px solid #d9d9e3;">
        </label>

        <label style="display:block; margin-bottom:10px;">
            Password:
            <input type="password" name="password" required style="width:100%; padding:10px; border-radius:8px; border:1.5px solid #d9d9e3;">
        </label>

        <label style="display:block; margin-bottom:8px; font-weight:bold;">
            CAPTCHA:
        </label>

        <div style="margin-bottom:8px;">
            <!-- No refresh control: image will show current session captcha value and will change on page load -->
            <img
                src="../includes/captcha.php"
                alt="CAPTCHA"
                id="captcha-img"
                style="border-radius:6px; border:1px solid #ddd; height:56px;"
            >
        </div>

        <label style="display:block; margin-bottom:14px;">
            <input type="text" name="captcha" required placeholder="Enter code above" style="width:100%; padding:10px; border-radius:8px; border:1.5px solid #d9d9e3;">
        </label>

        <div style="display:flex; gap:12px; align-items:center;">
            <button type="submit" class="user-login-form button">Login</button>
        </div>
        <p>Already have an account? <a href="user_login.php">Login here</a></p>

        <p style="margin-top:18px;">
            <a href="forgot_password.php">Forgot Password?</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="forgot_email.php">Forgot Email?</a>
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>