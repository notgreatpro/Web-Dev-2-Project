<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info from the database, including avatar
$stmt = $pdo->prepare("SELECT username, avatar, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User not found (should not happen)
    echo "User not found!";
    exit;
}
?>

<div class="profile-container">
    <h1>My Profile</h1>
    <?php if (!empty($user['avatar'])): ?>
        <img src="/public/avatars/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" style="width:90px;height:90px;border-radius:50%;">
    <?php endif; ?>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Registered:</strong> <?= htmlspecialchars($user['created_at']) ?></p>

    <a href="change_password.php">Change Password</a>
    <hr>
    <h2>My Comments</h2>
    <p>You haven't posted any comments yet.</p>
    <form action="delete_account.php" method="post">
        <button type="submit" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');" style="background:#a22;color:#fff;padding:10px 18px;border-radius:8px;font-weight:bold;">Delete My Account</button>
    </form>
</div>