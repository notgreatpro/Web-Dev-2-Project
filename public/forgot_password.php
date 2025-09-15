<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")
                ->execute([$token, $expires, $user['id']]);

            // Send email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
            $subject = "Reset your password";
            $message = "To reset your password, click:\n$reset_link\n\nThis link will expire in 1 hour.";
            mail($email, $subject, $message, "From: noreply@genshinexplorer.com");

            $msg = "If we found your email, instructions were sent.";
        } else {
            $msg = "If we found your email, instructions were sent.";
        }
    } else {
        $msg = "Please enter your email address.";
    }
}
?>
<link rel="stylesheet" href="css/forgot.css">
<div class="container" style="max-width:500px;">
    <h2>Forgot Password</h2>
    <?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post">
        <label>Email:<input type="email" name="email" required></label><br>
        <button type="submit" class="forgot-btn">Send Instructions</button>
    </form>
    <p><a href="user_login.php">&larr; Back to login</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>