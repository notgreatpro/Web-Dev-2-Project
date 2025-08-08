<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

$name = $vision = $weapon = $rarity = $nation = $description = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"] ?? "");
    $vision = trim($_POST["vision"] ?? "");
    $weapon = trim($_POST["weapon"] ?? "");
    $rarity = intval($_POST["rarity"] ?? 0);
    $nation = trim($_POST["nation"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Basic validation
    if ($name === "")   $errors[] = "Name is required.";
    if ($vision === "") $errors[] = "Vision is required.";
    if ($weapon === "") $errors[] = "Weapon is required.";
    if ($rarity < 1)    $errors[] = "Rarity must be a positive number.";
    if ($nation === "") $errors[] = "Nation is required.";
    if ($description === "") $errors[] = "Description is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO characters (`name`, `vision`, `signature weapons`, `character rarity`, `nations`, `description`)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $vision, $weapon, $rarity, $nation, $description]);

        // Handle image upload
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $target = "../img/" . $name . "." . strtolower($ext);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target);
        }

        header("Location: manage_characters.php");
        exit;
    }
}
?>

<div class="container" style="max-width:600px;">
    <h2>Add New Character</h2>
    <?php if($errors): ?>
        <div style="color:red;">
            <?= implode("<br>", array_map("htmlspecialchars", $errors)) ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <th>Name</th>
                <td><input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required></td>
            </tr>
            <tr>
                <th>Vision</th>
                <td><input type="text" name="vision" value="<?= htmlspecialchars($vision) ?>" required></td>
            </tr>
            <tr>
                <th>Weapon</th>
                <td><input type="text" name="weapon" value="<?= htmlspecialchars($weapon) ?>" req   uired></td>
            </tr>
            <tr>
                <th>Rarity</th>
                <td>
                    <select name="rarity" required>
                    <option value="4" <?= $rarity == 4 ? "selected" : "" ?>>4 Star</option>
                    <option value="5" <?= $rarity == 5 ? "selected" : "" ?>>5 Star</option>
                </select>
            </tr>
            <tr>
                <th>Nation</th>
                <td><input type="text" name="nation" value="<?= htmlspecialchars($nation) ?>" required></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><textarea name="description" rows="4" style="width:100%;" required><?= htmlspecialchars($description) ?></textarea></td>
            </tr>
            <tr>
                <th>Character Image</th>
                <td><input type="file" name="image" accept=".jpg,.jpeg,.png,.gif"></td>
            </tr>
        </table>
        <br>
        <button type="submit" class="btn">Add Character</button>
        <a href="manage_characters.php" class="btn" style="background:#ccc;color:#222;">Cancel</a>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>