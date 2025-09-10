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

// Handle profile update (username and avatar)
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Username update
    $new_username = trim($_POST['username'] ?? '');
    if ($new_username === '') {
        $errors[] = "Username cannot be empty.";
    } elseif ($new_username !== $user['username']) {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->fetch()) $errors[] = "Username already taken.";
    }

    // Avatar upload
    $avatar_filename = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/png', 'image/jpeg', 'image/gif'];
        if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
            $errors[] = "Only PNG, JPG, or GIF images allowed.";
        } else {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $avatar_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $dest = __DIR__ . "/img/" . $avatar_filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                // Optionally, delete old avatar file if not default
                if (!empty($user['avatar']) && $user['avatar'] !== 'default_avatar.png' && file_exists(__DIR__ . "/img/" . $user['avatar'])) {
                    @unlink(__DIR__ . "/img/" . $user['avatar']);
                }
            } else {
                $errors[] = "Avatar upload failed.";
                $avatar_filename = $user['avatar'];
            }
        }
    }

    // If no errors, update DB
    if (!$errors) {
        $sql = "UPDATE users SET username = ?";
        $params = [$new_username];
        if ($avatar_filename !== $user['avatar']) {
            $sql .= ", avatar = ?";
            $params[] = $avatar_filename;
        }
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $pdo->prepare($sql)->execute($params);
        // Update user info for session
        $user['username'] = $new_username;
        $user['avatar'] = $avatar_filename;
        $success = "Profile updated!";
    }
}

// Fetch user's comments across all characters
$user_comments = getUserCommentsWithCharacters($pdo, $user_id);
?>

<link rel="stylesheet" href="/public/css/profile.css">

<div class="container">
    <div class="profile-flex">
        <div class="profile-avatar-col">
            <?php
            $avatarFile = (!empty($user['avatar']) && file_exists(__DIR__ . "/img/" . $user['avatar']))
                ? "/public/img/" . htmlspecialchars($user['avatar'])
                : "/public/img/default_avatar.png";
            ?>
            <img class="profile-avatar" src="<?= $avatarFile ?>" alt="Avatar"
                 onerror="this.onerror=null;this.src='/public/img/default_avatar.png';">
        </div>
        <div class="profile-info-col">
            <h1>My Profile</h1>
            <?php if (!empty($errors)): ?>
                <div class="error"><?= implode('<br>', $errors) ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data" style="margin-bottom:1em;">
                <label for="username"><strong>Username:</strong></label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                <label for="avatar"><strong>Profile Avatar:</strong></label>
                <input type="file" name="avatar" id="avatar" accept="image/png, image/jpeg, image/gif">
                <button type="submit" name="update_profile" style="margin-top:0.6em;">Update Profile</button>
            </form>
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
        <button type="submit" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');" class="delete-btn">Delete My Account</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>