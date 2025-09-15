<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username) {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['email'])) {
            // Option 1: Display the email (not best for privacy)
            $msg = "Your registered email is: <b>" . htmlspecialchars($user['email']) . "</b>";
            // Option 2: Send email reminder (better privacy)
            // $msg = "If we found your username, instructions were sent to your registered email.";
            // mail($user['email'], "Email reminder", "This is a reminder that your registered email is: " . $user['email'], "From: noreply@genshinexplorer.com");
        } else {
            $msg = "If we found your username, instructions were sent to your registered email.";
        }
    } else {
        $msg = "Please enter your username.";
    }
}
?>
<link rel="stylesheet" href="css/forgot.css">
<div class="container" style="max-width:500px;">
    <h2>Forgot Email</h2>
    <?php if ($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>
    <form method="post">
        <label>Username:<input type="text" name="username" required></label><br>
        <button type="submit" class="forgot-btn">Send Instructions</button>
    </form>
    <p><a href="user_login.php">&larr; Back to login</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>