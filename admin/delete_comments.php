<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: manage_comments.php");
exit;
?>