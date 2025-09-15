<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '') $errors[] = "Username required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if ($password === '') $errors[] = "Password required.";
    if ($password !== $password2) $errors[] = "Passwords do not match.";

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) $errors[] = "Username already taken.";

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already used.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hash]);
        header("Location: user_login.php?signup=success");
        exit;
    }
}
?>
<link rel="stylesheet" href="css/signup.css">
<div class="signup-container">
    <h2>Sign Up</h2>
    <?php if ($errors): ?>
        <div class="error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>
    <form class="signup-form" method="post">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <label>Email:
            <input type="email" name="email" required>
        </label>
        <label>Password:
            <input type="password" name="password" required>
        </label>
        <label>Repeat Password:
            <input type="password" name="password2" required>
        </label>
        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="user_login.php">Login here</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>