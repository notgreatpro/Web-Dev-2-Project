<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/header.php';

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: manage_characters.php");
    exit;
}

// Get character for editing
$stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
$stmt->execute([$id]);
$character = $stmt->fetch();

if (!$character) {
    echo "<div class='container'>Character not found. <a href='manage_characters.php'>Back</a></div>";
    require_once '../includes/footer.php';
    exit;
}

$name   = $character["name"];
$vision = $character["vision"];
$weapon = $character["signature weapons"];
$rarity = $character["character rarity"];
$nation = $character["nations"];
$description = $character["description"];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"] ?? "");
    $vision = trim($_POST["vision"] ?? "");
    $weapon = trim($_POST["weapon"] ?? "");
    $rarity = intval($_POST["rarity"] ?? 0);
    $nation = trim($_POST["nation"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($name === "")   $errors[] = "Name is required.";
    if ($vision === "") $errors[] = "Vision is required.";
    if ($weapon === "") $errors[] = "Weapon is required.";
    if ($rarity < 1)    $errors[] = "Rarity must be a positive number.";
    if ($nation === "") $errors[] = "Nation is required.";
    if ($description === "") $errors[] = "Description is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE characters SET `name`=?, `vision`=?, `signature weapons`=?, `character rarity`=?, `nations`=?, `description`=? WHERE id=?");
        $stmt->execute([$name, $vision, $weapon, $rarity, $nation, $description, $id]);
        header("Location: manage_characters.php");
        exit;
    }
}
?>

<div class="container" style="max-width:500px;">
    <h2>Edit Character</h2>
    <?php if($errors): ?>
        <div style="color:red;">
            <?= implode("<br>", array_map("htmlspecialchars", $errors)) ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <label>Name:<br><input type="text" name="name" value="<?= htmlspecialchars($name) ?>"></label><br><br>
        <label>Vision:<br><input type="text" name="vision" value="<?= htmlspecialchars($vision) ?>"></label><br><br>
        <label>Weapon:<br><input type="text" name="weapon" value="<?= htmlspecialchars($weapon) ?>"></label><br><br>
        <label>Rarity:<br><input type="number" name="rarity" min="1" max="5" value="<?= htmlspecialchars($rarity) ?>"></label><br><br>
        <label>Nation:<br><input type="text" name="nation" value="<?= htmlspecialchars($nation) ?>"></label><br><br>
        <label>Description:<br>
            <textarea name="description" rows="4" style="width:100%;"><?= htmlspecialchars($description) ?></textarea>
        </label><br><br>
        <button type="submit">Save Changes</button>
        <a href="manage_characters.php">Cancel</a>
    </form>
</div>