<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");
$result = $conn->query("SELECT * FROM characters");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Character List (Admin)</title>
</head>
<body>
    <h1>Character List (Admin)</h1>
    <table border="1" cellpadding="5">
        <tr>
            <th>Name</th>
            <th>Rarity</th>
            <th>Vision</th>
            <th>Birthday</th>
            <th>Affiliation</th>
            <th>Nations</th>
            <th>Signature Weapons</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <a href="character_detail.php?id=<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['character rarity']); ?></td>
            <td><?php echo htmlspecialchars($row['vision']); ?></td>
            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
            <td><?php echo htmlspecialchars($row['affiliation']); ?></td>
            <td><?php echo htmlspecialchars($row['nations']); ?></td>
            <td><?php echo htmlspecialchars($row['signature weapons']); ?></td>
            <td><a href="edit_character.php?id=<?php echo $row['id']; ?>">Edit</a></td>
            <td><a href="delete_character.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this character?');">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="index.php">Add New Character</a> |
    <a href="logout.php">Logout</a> |
    <a href="home.php">Return to Home</a>
</body>
</html>