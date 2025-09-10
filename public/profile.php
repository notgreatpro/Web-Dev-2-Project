<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
// Get user info
$stmt = $pdo->prepare("SELECT username, avatar, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    require_once '../includes/footer.php';
    exit;
}

// Fetch user's comments across all characters
$user_comments = getUserCommentsWithCharacters($pdo, $user_id);
?>

<link rel="stylesheet" href="/public/css/profile.css">

<div class="container">
    <div class="profile-flex">
        <div class="profile-avatar-col">
            <?php
            // Set the correct avatar path for display
            $avatarFile = (!empty($user['avatar']) && file_exists(__DIR__ . "/img/" . $user['avatar']))
                ? "/public/img/" . htmlspecialchars($user['avatar'])
                : "/public/img/default_avatar.png";
            ?>
            <img class="profile-avatar" src="<?= $avatarFile ?>" alt="Avatar"
                 onerror="this.onerror=null;this.src='/public/img/default_avatar.png';">
        </div>
        <div class="profile-info-col">
            <h1>My Profile</h1>
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Registered:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            <a href="change_password.php">Change Password</a>
        </div>
    </div>
    <hr>
    <h2>My Comments</h2>
    <?php if ($user_comments): ?>
        <ul class="user-comments-list">
            <?php foreach ($user_comments as $comment): ?>
                <li>
                    <div>
                        <span class="comment-character"><b><?= htmlspecialchars($comment['character_name']) ?></b></span>
                        <span class="comment-time"><?= htmlspecialchars($comment['created_at']) ?></span>
                    </div>
                    <div class="comment-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You haven't posted any comments yet.</p>
    <?php endif; ?>
    <form action="delete_account.php" method="post">
        <button type="submit" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');" style="background:#a22;color:#fff;padding:10px 18px;border-radius:8px;font-weight:bold;">Delete My Account</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>