<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data from $_POST here
    $name = $_POST['name'];
    $rarity = $_POST['character_rarity'];
    $vision = $_POST['vision'];
    $birthday = $_POST['birthday'];
    $affiliation = $_POST['affiliation'];
    $nations = $_POST['nations'];
    $signature_weapons = $_POST['signature_weapons'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("INSERT INTO characters (name, `character rarity`, vision, birthday, affiliation, nations, `signature weapons`, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $rarity, $vision, $birthday, $affiliation, $nations, $signature_weapons, $description);
    $stmt->execute();
    $stmt->close();

    // Redirect to character list (admin)
    header("Location: characters_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Character</title>
</head>
<body>
    <h1>Add New Character</h1>
    <form method="post">
        Name: <input type="text" name="name" required><br>
        Character Rarity:
        <select name="character_rarity" required>
            <option value="">-- Select Rarity --</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select><br>
        Vision: <input type="text" name="vision" required><br>
        Birthday: <input type="date" name="birthday"><br>
        Affiliation: <input type="text" name="affiliation"><br>
        Nations:
        <select name="nations" required>
            <option value="">-- Select Nation --</option>
            <option value="Mondstadt">Mondstadt</option>
            <option value="Liyue">Liyue</option>
            <option value="Inazuma">Inazuma</option>
            <option value="Sumeru">Sumeru</option>
            <option value="Fontaine">Fontaine</option>
            <option value="Natlan">Natlan</option>
            <option value="Snezhnaya">Snezhnaya</option>
        </select><br>
        Signature Weapons: <input type="text" name="signature_weapons"><br>
        Description:<br>
        <textarea name="description" rows="4" cols="70" placeholder="Enter character description here..."></textarea><br>
        <input type="submit" value="Add Character">
    </form>
    <br>
    <a href="characters_list.php">Return to Character List</a> |
    <a href="home.php">Return to Home</a>
</body>
</html>