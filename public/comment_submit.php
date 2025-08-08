<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $character_id = isset($_POST['character_id']) ? intval($_POST['character_id']) : 0;
    $content = trim($_POST['content'] ?? '');

    // Simple validation
    if ($character_id > 0 && $content !== '') {
        addComment($pdo, $character_id, $content);
        // Redirect back to the character page
        header("Location: character.php?id=" . $character_id);
        exit;
    } else {
        // Invalid input: you could redirect back with an error, or handle as needed
        header("Location: character.php?id=" . $character_id . "&error=empty");
        exit;
    }
} else {
    // Direct access
    header("Location: index.php");
    exit;
}
?>