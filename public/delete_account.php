<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Optionally, delete user's comments, avatars, etc. first

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

session_unset();
session_destroy();
header("Location: user_login.php?account_deleted=1");
exit;
?>