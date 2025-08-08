<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: manage_characters.php");
    exit;
}

// Get character
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
$affiliation = $character["affiliation"];
$birthday = $character["birthday"];
$imageFileName = $character["image"];
$quote = $character["quote"];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"] ?? "");
    $vision = trim($_POST["vision"] ?? "");
    $weapon = trim($_POST["weapon"] ?? "");
    $rarity = intval($_POST["rarity"] ?? 0);
    $nation = trim($_POST["nation"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $affiliation = trim($_POST["affiliation"] ?? "");
    $birthday = trim($_POST["birthday"] ?? "");
    $quote = trim($_POST["quote"] ?? "");

    // Image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
        $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = preg_replace('/[^a-zA-Z0-9]/', '', $name) . "_" . time() . "." . $ext;
            $targetDir = __DIR__ . '/../public/img/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $targetPath = $targetDir . $newFileName;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                // Optionally delete old image
                if ($imageFileName && file_exists($targetDir . $imageFileName)) {
                    unlink($targetDir . $imageFileName);
                }
                $imageFileName = $newFileName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Invalid image file type.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE characters SET `name`=?, `vision`=?, `signature weapons`=?, `character rarity`=?, `nations`=?, `description`=?, `affiliation`=?, `birthday`=?, `image`=?, `quote`=? WHERE id=?");
        $stmt->execute([$name, $vision, $weapon, $rarity, $nation, $description, $affiliation, $birthday, $imageFileName, $quote, $id]);
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
            <tr><th>Name</th><td><input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required></td></tr>
            <tr><th>Vision</th><td><input type="text" name="vision" value="<?= htmlspecialchars($vision) ?>" required></td></tr>
            <tr><th>Weapon</th><td><input type="text" name="weapon" value="<?= htmlspecialchars($weapon) ?>" required></td></tr>
            <tr><th>Rarity</th>
                <td>
                    <select name="rarity" required>
                        <option value="4" <?= $rarity == 4 ? "selected" : "" ?>>4 Star</option>
                        <option value="5" <?= $rarity == 5 ? "selected" : "" ?>>5 Star</option>
                    </select>
                </td>
            </tr>
            <tr><th>Nation</th><td><input type="text" name="nation" value="<?= htmlspecialchars($nation) ?>" required></td></tr>
            <tr><th>Description</th><td><textarea name="description" rows="4" style="width:100%;" required><?= htmlspecialchars($description) ?></textarea></td></tr>
            <tr><th>Affiliation</th><td><input type="text" name="affiliation" value="<?= htmlspecialchars($affiliation) ?>" required></td></tr>
            <tr><th>Birthday</th><td><input type="date" name="birthday" value="<?= htmlspecialchars($birthday) ?>" required></td></tr>
            <tr>
                <th>Character Quote</th>
                <td>
                    <input type="text" name="quote" value="<?= htmlspecialchars($quote) ?>" maxlength="255" placeholder="Enter character quote">
                </td>
            </tr>
            <tr>
                <th>Character Image</th>
                <td>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($imageFileName && file_exists(__DIR__ . '/../public/img/' . $imageFileName)): ?>
                        <br>
                        <img src="../public/img/<?= htmlspecialchars($imageFileName) ?>" alt="Current image" style="width:100px;height:100px;border-radius:10px;">
                    <?php endif; ?>
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