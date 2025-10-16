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
    // Prevent duplicate likes (character_likes table logic)
    if (!hasUserLikedCharacter($pdo, $user_id, $character_id)) {
        $stmt = $pdo->prepare("UPDATE characters SET likes = likes + 1 WHERE id = ?");
        $stmt->execute([$character_id]);
        $stmt = $pdo->prepare("INSERT INTO character_likes (user_id, character_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $character_id]);
    }
    header("Location: character.php?id=" . $character_id);
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>