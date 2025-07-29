<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid character ID.";
    exit;
}

$errors = [];

// Fetch character details for pre-filling
$stmt = $conn->prepare("SELECT * FROM characters WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$character = $result->fetch_assoc();
$stmt->close();

if (!$character) {
    echo "Character not found.";
    exit;
}

// If form submitted, validate and update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $rarity = $_POST['character_rarity'];
    $vision = trim($_POST['vision']);
    $birthday = trim($_POST['birthday']);
    $affiliation = trim($_POST['affiliation']);
    $nations = $_POST['nations'];
    $signature_weapons = trim($_POST['signature_weapons']);
    $description = trim($_POST['description']);

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($rarity) || !in_array($rarity, ['4', '5'])) {
        $errors[] = "Character Rarity must be 4 or 5.";
    }
    if (empty($vision)) {
        $errors[] = "Vision is required.";
    }
    if (!empty($birthday) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday)) {
        $errors[] = "Birthday must be in YYYY-MM-DD format.";
    }
    $allowed_nations = ['Mondstadt', 'Liyue', 'Inazuma', 'Sumeru', 'Fontaine', 'Natlan', 'Snezhnaya'];
    if (empty($nations) || !in_array($nations, $allowed_nations)) {
        $errors[] = "Nations must be selected from the list.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE characters SET name=?, `character rarity`=?, vision=?, birthday=?, affiliation=?, nations=?, `signature weapons`=?, description=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $name, $rarity, $vision, $birthday, $affiliation, $nations, $signature_weapons, $description, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: characters_list.php");
        exit;
    }
} else {
    // Pre-fill form values from database
    $name = $character['name'];
    $rarity = $character['character rarity'];
    $vision = $character['vision'];
    $birthday = $character['birthday'];
    $affiliation = $character['affiliation'];
    $nations = $character['nations'];
    $signature_weapons = $character['signature weapons'];
    $description = $character['description'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Character</title>
</head>
<body>
    <h1>Edit Character</h1>
    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post">
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
        Character Rarity:
        <select name="character_rarity" required>
            <option value="">-- Select Rarity --</option>
            <option value="4" <?php if($rarity=='4') echo 'selected'; ?>>4</option>
            <option value="5" <?php if($rarity=='5') echo 'selected'; ?>>5</option>
        </select><br>
        Vision: <input type="text" name="vision" value="<?php echo htmlspecialchars($vision); ?>" required><br>
        Birthday: <input type="date" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>"><br>
        Affiliation: <input type="text" name="affiliation" value="<?php echo htmlspecialchars($affiliation); ?>"><br>
        Nations:
        <select name="nations" required>
            <option value="">-- Select Nation --</option>
            <?php
            foreach (['Mondstadt', 'Liyue', 'Inazuma', 'Sumeru', 'Fontaine', 'Natlan', 'Snezhnaya'] as $nation) {
                echo '<option value="'.htmlspecialchars($nation).'"';
                if ($nations === $nation) echo ' selected';
                echo '>'.htmlspecialchars($nation).'</option>';
            }
            ?>
        </select><br>
        Signature Weapons: <input type="text" name="signature_weapons" value="<?php echo htmlspecialchars($signature_weapons); ?>"><br>
        Description:<br>
        <textarea name="description" rows="4" cols="70" placeholder="Enter character description here..."><?php echo htmlspecialchars($description); ?></textarea><br>
        <input type="submit" value="Update Character">
    </form>
    <br>
    <a href="characters_list.php">Return to Character List</a> |
    <a href="home.php">Return to Home</a>
</body>
</html>