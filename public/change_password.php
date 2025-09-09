<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: user_login.php");
    exit;
}

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        $errors[] = "Current password is incorrect.";
    } elseif (strlen($new) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } elseif ($new !== $repeat) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        $success = "Password changed successfully!";
    }
}
?>
<div class="container" style="max-width:500px;">
    <h2>Change Password</h2>
    <?php if ($errors): ?>
        <div class="error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Current Password: <input type="password" name="current_password" required></label><br>
        <label>New Password: <input type="password" name="new_password" required></label><br>
        <label>Repeat New Password: <input type="password" name="repeat_password" required></label><br>
        <button type="submit">Change Password</button>
    </form>
    <p><a href="profile.php">&larr; Back to Profile</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>