<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = intval($_GET["id"] ?? 0);

if ($id > 0) {
    // Get the image filename before deleting the character
    $stmt = $pdo->prepare("SELECT image FROM characters WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && !empty($row['image'])) {
        $imgPath = __DIR__ . "/../public/img/" . $row['image'];
        // Only attempt to delete if the file exists and is not default.png
        if (file_exists($imgPath) && $row['image'] !== 'default.png') {
            unlink($imgPath);
        }
    }

    // Delete character from DB
    $stmt = $pdo->prepare("DELETE FROM characters WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: manage_characters.php");
exit;
?>