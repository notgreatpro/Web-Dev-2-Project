<?php
// This is the public detail page, no login required!
$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid character ID.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM characters WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$character = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$character) {
    echo "Character not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($character['name']); ?> - Detail</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($character['name']); ?></h1>
    <?php if (!empty($character['description'])): ?>
        <p><b>Description:</b> <?php echo nl2br(htmlspecialchars($character['description'])); ?></p>
    <?php endif; ?>
    <ul>
        <li><b>Character Rarity:</b> <?php echo htmlspecialchars($character['character rarity']); ?></li>
        <li><b>Vision:</b> <?php echo htmlspecialchars($character['vision']); ?></li>
        <li><b>Birthday:</b> <?php echo htmlspecialchars($character['birthday']); ?></li>
        <li><b>Affiliation:</b> <?php echo htmlspecialchars($character['affiliation']); ?></li>
        <li><b>Nations:</b> <?php echo htmlspecialchars($character['nations']); ?></li>
        <li><b>Signature Weapons:</b> <?php echo htmlspecialchars($character['signature weapons']); ?></li>
    </ul>
    <p>
        <a href="character_list_public.php">Back to List</a>
    </p>
</body>
</html>