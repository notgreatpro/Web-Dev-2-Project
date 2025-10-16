<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_logged_in'], $_SESSION['user_id']) || !$_SESSION['user_logged_in']) {
    header("Location: user_login.php");
    exit;
}

$character_id = intval($_POST['character_id'] ?? 0);
$user_id = intval($_SESSION['user_id']);

if ($character_id > 0) {
    // Check if user liked the character
    if (hasUserLikedCharacter($pdo, $user_id, $character_id)) {
        // Remove like record
        $stmt = $pdo->prepare("DELETE FROM character_likes WHERE user_id = ? AND character_id = ?");
        $stmt->execute([$user_id, $character_id]);
        // Decrease like count
        $stmt = $pdo->prepare("UPDATE characters SET likes = likes - 1 WHERE id = ? AND likes > 0");
        $stmt->execute([$character_id]);
    }
    header("Location: character.php?id=" . $character_id);
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>