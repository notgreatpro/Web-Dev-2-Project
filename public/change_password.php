<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Remove session_start() if already in header.php!
$user_id = $_SESSION['user_id'] ?? null;
$msg = '';

if (!$user_id) {
    header('Location: user_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    // Fetch current password hash
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current, $user['password'])) {
        $msg = "Current password is incorrect.";
    } elseif (strlen($new) < 6) {
        $msg = "New password must be at least 6 characters.";
    } elseif ($new !== $repeat) {
        $msg = "New passwords do not match.";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_hash, $user_id]);
        $msg = "success: Password updated successfully!";
    }
}
?>
<link rel="stylesheet" href="css/account.css">
<div class="change-password-container">
    <h2>Change Password</h2>
    <?php if ($msg): ?>
      <div class="<?= strpos($msg, 'success:') === 0 ? 'success' : 'error' ?>">
        <?= strpos($msg, 'success:') === 0 ? htmlspecialchars(substr($msg,9)) : htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>
    <form class="change-password-form" method="post">
        <label>
            Current Password:
            <input type="password" name="current_password" required>
        </label>
        <label>
            New Password:
            <input type="password" name="new_password" required>
        </label>
        <label>
            Repeat New Password:
            <input type="password" name="repeat_password" required>
        </label>
        <button type="submit" class="change-password-btn">Change Password</button>
    </form>
    <p><a href="profile.php">&larr; Back to Profile</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>