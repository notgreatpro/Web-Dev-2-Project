<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = trim($_POST['name']);
$character_rairity = intval($_POST['character_rarity']); // Use the POST name, but match DB column
$vision = trim($_POST['vision']);
$birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
$affiliation = trim($_POST['affiliation']);
$nations = trim($_POST['nations']);
$signature_weapons = trim($_POST['signature_weapons']);

// Prepare and execute insert
$stmt = $conn->prepare("INSERT INTO characters (name, `character rarity`, vision, birthday, affiliation, nations, `signature weapons`) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssss", $name, $character_rairity, $vision, $birthday, $affiliation, $nations, $signature_weapons);

if ($stmt->execute()) {
    header("Location: characters_list.php?success=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>