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
$normal_attack_name = $character["normal_attack_name"];
$normal_attack_description = $character["normal_attack_description"];
$skill_name = $character["skill_name"];
$skill_description = $character["skill_description"];
$burst_name = $character["burst_name"];
$burst_description = $character["burst_description"];
$passive1_name = $character["passive1_name"];
$passive1_description = $character["passive1_description"];
$passive2_name = $character["passive2_name"];
$passive2_description = $character["passive2_description"];
$utility_passive_name = $character["utility_passive_name"];
$utility_passive_description = $character["utility_passive_description"];
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
    $normal_attack_name = trim($_POST["normal_attack_name"] ?? "");
    $normal_attack_description = trim($_POST["normal_attack_description"] ?? "");
    $skill_name = trim($_POST["skill_name"] ?? "");
    $skill_description = trim($_POST["skill_description"] ?? "");
    $burst_name = trim($_POST["burst_name"] ?? "");
    $burst_description = trim($_POST["burst_description"] ?? "");
    $passive1_name = trim($_POST["passive1_name"] ?? "");
    $passive1_description = trim($_POST["passive1_description"] ?? "");
    $passive2_name = trim($_POST["passive2_name"] ?? "");
    $passive2_description = trim($_POST["passive2_description"] ?? "");
    $utility_passive_name = trim($_POST["utility_passive_name"] ?? "");
    $utility_passive_description = trim($_POST["utility_passive_description"] ?? "");

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
        $stmt = $pdo->prepare("UPDATE characters SET 
            `name`=?, `vision`=?, `signature weapons`=?, `character rarity`=?, `nations`=?, `description`=?, `affiliation`=?, `birthday`=?, `image`=?, `quote`=?,
            `normal_attack_name`=?, `normal_attack_description`=?,
            `skill_name`=?, `skill_description`=?, `burst_name`=?, `burst_description`=?,
            `passive1_name`=?, `passive1_description`=?, `passive2_name`=?, `passive2_description`=?,
            `utility_passive_name`=?, `utility_passive_description`=?
            WHERE id=?");
        $stmt->execute([
            $name, $vision, $weapon, $rarity, $nation, $description, $affiliation, $birthday, $imageFileName, $quote,
            $normal_attack_name, $normal_attack_description,
            $skill_name, $skill_description, $burst_name, $burst_description,
            $passive1_name, $passive1_description, $passive2_name, $passive2_description,
            $utility_passive_name, $utility_passive_description,
            $id
        ]);
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
            <tr><th>Signature Weapon</th><td><input type="text" name="weapon" value="<?= htmlspecialchars($weapon) ?>" required></td></tr>
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
            <tr><th>Normal Attack Name</th><td><input type="text" name="normal_attack_name" value="<?= htmlspecialchars($normal_attack_name) ?>"></td></tr>
            <tr><th>Normal Attack Description</th><td><textarea name="normal_attack_description" rows="2" style="width:100%;"><?= htmlspecialchars($normal_attack_description) ?></textarea></td></tr>
            <tr><th>Elemental Skill Name</th><td><input type="text" name="skill_name" value="<?= htmlspecialchars($skill_name) ?>"></td></tr>
            <tr><th>Elemental Skill Description</th><td><textarea name="skill_description" rows="2" style="width:100%;"><?= htmlspecialchars($skill_description) ?></textarea></td></tr>
            <tr><th>Elemental Burst Name</th><td><input type="text" name="burst_name" value="<?= htmlspecialchars($burst_name) ?>"></td></tr>
            <tr><th>Elemental Burst Description</th><td><textarea name="burst_description" rows="2" style="width:100%;"><?= htmlspecialchars($burst_description) ?></textarea></td></tr>
            <tr><th>1st Ascension Passive Name</th><td><input type="text" name="passive1_name" value="<?= htmlspecialchars($passive1_name) ?>"></td></tr>
            <tr><th>1st Ascension Passive Description</th><td><textarea name="passive1_description" rows="2" style="width:100%;"><?= htmlspecialchars($passive1_description) ?></textarea></td></tr>
            <tr><th>4th Ascension Passive Name</th><td><input type="text" name="passive2_name" value="<?= htmlspecialchars($passive2_name) ?>"></td></tr>
            <tr><th>4th Ascension Passive Description</th><td><textarea name="passive2_description" rows="2" style="width:100%;"><?= htmlspecialchars($passive2_description) ?></textarea></td></tr>
            <tr><th>Utility Passive Name</th><td><input type="text" name="utility_passive_name" value="<?= htmlspecialchars($utility_passive_name) ?>"></td></tr>
            <tr><th>Utility Passive Description</th><td><textarea name="utility_passive_description" rows="2" style="width:100%;"><?= htmlspecialchars($utility_passive_description) ?></textarea></td></tr>
        </table>
        <br>
        <div class="admin-form-actions">
            <button type="submit" class="btn">Save Changes</button>
            <a href="manage_characters.php" class="btn" style="background:#ccc;color:#222;">Cancel</a>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>