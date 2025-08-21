<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<link rel="stylesheet" href="css/user_login.css">
<div class="user-login-container">
    <h2>User Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form class="user-login-form" method="post">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <label>Password:
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>
    <p>No account yet? <a href="signup.php">Sign up here</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>