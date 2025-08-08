<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, admin!</p>
    <ul>
        <li><a href="manage_characters.php">Manage Characters</a></li>
        <li><a href="manage_comments.php">Manage Comments</a></li>
        <li><a href="../public/index.php">Back to Site</a></li>
        <li><a href="logout.php">Log Out</a></li>
    </ul>
</div>

<?php require_once '../includes/footer.php'; ?>