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
$user_id = $_SESSION['user_id'];

// Fetch existing user data
$stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$username = $user['username'];
$avatar = $user['avatar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    // Check if username is available (and not the same as current)
    if ($new_username !== $username) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
        $check->execute([$new_username, $user_id]);
        if ($check->fetch()) {
            $errors[] = "Username already taken.";
        }
    }
    // Avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $avatar_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $targetDir = __DIR__ . '/../public/avatars/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $targetPath = $targetDir . $avatar_name;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                // Optionally remove old avatar
                if ($avatar && file_exists($targetDir . $avatar)) {
                    unlink($targetDir . $avatar);
                }
                $avatar = $avatar_name;
            } else {
                $errors[] = "Failed to upload avatar.";
            }
        } else {
            $errors[] = "Invalid avatar file type.";
        }
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$new_username, $avatar, $user_id]);
        $success = "Profile updated!";
        $username = $new_username;
    }
}
?>
<link rel="stylesheet" href="css/profile.css">
<div class="profile-container">
    <h2>Edit Profile</h2>
    <?php if ($errors): ?>
        <div class="error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Username:
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
        </label>
        <br>
        <label>Avatar:
            <input type="file" name="avatar" accept="image/*">
            <?php if ($avatar && file_exists(__DIR__ . '/../public/avatars/' . $avatar)): ?>
                <br>
                <img src="../public/avatars/<?= htmlspecialchars($avatar) ?>" alt="Current avatar" style="width:70px;height:70px;border-radius:50%;">
            <?php endif; ?>
        </label>
        <br><br>
        <button type="submit">Save Changes</button>
    </form>
    <p><a href="profile.php">&larr; Back to Profile</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>