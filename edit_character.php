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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $character_rairity = intval($_POST['character_rarity']);
    $vision = trim($_POST['vision']);
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $affiliation = trim($_POST['affiliation']);
    $nations = trim($_POST['nations']);
    $signature_weapons = trim($_POST['signature_weapons']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE characters SET name=?, `character rarity`=?, vision=?, birthday=?, affiliation=?, nations=?, `signature weapons`=?, description=? WHERE id=?");
    $stmt->bind_param("sissssssi", $name, $character_rairity, $vision, $birthday, $affiliation, $nations, $signature_weapons, $description, $id);
    if ($stmt->execute()) {
        header("Location: characters_list.php?updated=1");
        exit;
    } else {
        echo "Error updating character: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch character data
$stmt = $conn->prepare("SELECT * FROM characters WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$character = $result->fetch_assoc();
if (!$character) {
    echo "Character not found.";
    exit;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Character</title>
</head>
<body>
    <h1>Edit Character</h1>
    <form method="post">
        <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($character['name']); ?>" required></label><br>
        <label>Character Rarity: 
            <select name="character_rarity" required>
                <option value="4" <?php if ($character['character rarity'] == 4) echo 'selected'; ?>>4 Stars</option>
                <option value="5" <?php if ($character['character rarity'] == 5) echo 'selected'; ?>>5 Stars</option>
            </select>
        </label><br>
        <label>Vision: <input type="text" name="vision" value="<?php echo htmlspecialchars($character['vision']); ?>" required></label><br>
        <label>Birthday: <input type="date" name="birthday" value="<?php echo htmlspecialchars($character['birthday']); ?>"></label><br>
        <label>Affiliation: <input type="text" name="affiliation" value="<?php echo htmlspecialchars($character['affiliation']); ?>"></label><br>
        <label>Nations:
            <select name="nations" required>
                <option value="Mondstadt" <?php if ($character['nations'] == 'Mondstadt') echo 'selected'; ?>>Mondstadt</option>
                <option value="Liyue" <?php if ($character['nations'] == 'Liyue') echo 'selected'; ?>>Liyue</option>
                <option value="Inazuma" <?php if ($character['nations'] == 'Inazuma') echo 'selected'; ?>>Inazuma</option>
                <option value="Sumeru" <?php if ($character['nations'] == 'Sumeru') echo 'selected'; ?>>Sumeru</option>
                <option value="Fontaine" <?php if ($character['nations'] == 'Fontaine') echo 'selected'; ?>>Fontaine</option>
                <option value="Natlan" <?php if ($character['nations'] == 'Natlan') echo 'selected'; ?>>Natlan</option>
            </select>
        </label><br>
        <label>Signature Weapons: <input type="text" name="signature_weapons" value="<?php echo htmlspecialchars($character['signature weapons']); ?>"></label><br>
        <label>Description:<br>
            <textarea name="description" rows="4" cols="50" placeholder="Enter character description here..."><?php echo htmlspecialchars($character['description']); ?></textarea>
        </label><br>
        <button type="submit">Update Character</button>
    </form>
    <p><a href="characters_list.php">Back to List</a></p>
</body>
</html>