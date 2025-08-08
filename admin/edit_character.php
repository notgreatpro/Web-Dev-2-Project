<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

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
    <h2>Edit Character</h2>
    <?php if($errors): ?>
        <div style="color:red;">
            <?= implode("<br>", array_map("htmlspecialchars", $errors)) ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <table class="admin-form-table">
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
                <td><input type="text" name="weapon" value="<?= htmlspecialchars($weapon) ?>" required></td>
            </tr>
            <tr>
                <th>Rarity</th>
                <td>
                     <select name="rarity" required>
                        <option value="4" <?= $rarity == 4 ? "selected" : "" ?>>4 Star</option>
                        <option value="5" <?= $rarity == 5 ? "selected" : "" ?>>5 Star</option>
                    </select>
                </td>
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
                <td>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif"><br>
                    <?php
                    $img_path = "../img/" . $name . ".jpg";
                    if (file_exists($img_path)) {
                        echo "<img src='$img_path' alt='$name' style='max-width:120px;border-radius:8px;margin-top:8px;'>";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <br>
        <div class="admin-form-actions">
            <button type="submit" class="btn">Save Changes</button>
            <a href="manage_characters.php" class="btn" style="background:#ccc;color:#222;">Cancel</a>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>