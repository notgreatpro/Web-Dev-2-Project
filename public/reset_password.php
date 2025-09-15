<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$msg = '';
$token = $_GET['token'] ?? '';
if (!$token) {
    $msg = "Invalid or expired link.";
} else {
    $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || strtotime($user['reset_expires']) < time()) {
        $msg = "Invalid or expired link.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass = $_POST['password'] ?? '';
        $pass2 = $_POST['password2'] ?? '';
        if ($pass === '' || $pass !== $pass2 || strlen($pass) < 6) {
            $msg = "Passwords must match and be at least 6 chars.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id = ?")
                ->execute([$hash, $user['id']]);
            $msg = "Password reset! <a href='user_login.php'>Login now</a>.";
        }
    }
}
?>
<link rel="stylesheet" href="css/forgot.css">
<div class="container" style="max-width:500px;">
    <h2>Reset Password</h2>
    <?php if ($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>
    <?php if (!$msg || strpos($msg, "reset!") === false): ?>
    <form method="post">
        <label>New Password:<input type="password" name="password" required></label><br>
        <label>Repeat Password:<input type="password" name="password2" required></label><br>
        <button type="submit" class="forgot-btn">Reset Password</button>
    </form>
    <?php endif; ?>
    <p><a href="user_login.php">&larr; Back to login</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>