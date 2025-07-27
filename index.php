<?php
session_start();

// Simple login check (replace with your real login system)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
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
    <form action="save_character.php" method="post">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Character Rarity: 
            <select name="character_rarity" required>
                <option value="">-- Select Rarity --</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
        </label><br>
        <label>Vision: <input type="text" name="vision" required></label><br>
        <label>Birthday: <input type="date" name="birthday"></label><br>
        <label>Affiliation: <input type="text" name="affiliation"></label><br>
        <label>Nations:
            <select name="nations" required>
                <option value="">-- Select Nation --</option>
                <option value="Mondstadt">Mondstadt</option>
                <option value="Liyue">Liyue</option>
                <option value="Inazuma">Inazuma</option>
                <option value="Sumeru">Sumeru</option>
                <option value="Fontaine">Fontaine</option>
                <option value="Natlan">Natlan</option>
            </select>
        </label><br>
        <label>Signature Weapons: <input type="text" name="signature_weapons"></label><br>
        <label>Description:<br>
            <textarea name="description" rows="4" cols="50" placeholder="Enter character description here..."></textarea>
        </label><br>
        <button type="submit">Add Character</button>
    </form>
</body>
</html>