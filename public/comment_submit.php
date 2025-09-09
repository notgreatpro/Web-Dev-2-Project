<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $character_id = isset($_POST['character_id']) ? intval($_POST['character_id']) : 0;
    $content = trim($_POST['content'] ?? '');
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    if ($character_id > 0 && $content !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (character_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$character_id, $user_id, $content]);
        header("Location: character.php?id=" . $character_id);
        exit;
    } else {
        header("Location: character.php?id=" . $character_id . "&error=empty");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>