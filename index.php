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
        <label>Vision: <input type="text" name="vision" required></label><br>
        <label>Birthday: <input type="date" name="birthday"></label><br>
        <label>Affiliation ID: <input type="number" name="affiliation_id"></label><br>
        <label>Nation ID: <input type="number" name="nation_id"></label><br>
        <button type="submit">Add Character</button>
    </form>
</body>
</html>