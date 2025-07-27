<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

// Sorting logic
$allowed = [
    'name', 'birthday', 'id', 
    'affiliation', 'vision', 'nations', 'signature weapons'
];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT * FROM characters ORDER BY `$sort` $order";
$result = $conn->query($sql);

// For toggling sort order in links
$nextOrder = $order === 'ASC' ? 'desc' : 'asc';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Character List</title>
</head>
<body>
    <h1>Character List</h1>
    <p>Sort by: 
        <a href="?sort=name&order=<?php echo $nextOrder; ?>">Name</a> | 
        <a href="?sort=birthday&order=<?php echo $nextOrder; ?>">Birthday</a> | 
        <a href="?sort=id&order=<?php echo $nextOrder; ?>">ID</a> |
        <a href="?sort=affiliation&order=<?php echo $nextOrder; ?>">Affiliation</a> |
        <a href="?sort=vision&order=<?php echo $nextOrder; ?>">Vision</a> |
        <a href="?sort=nations&order=<?php echo $nextOrder; ?>">Nations</a> |
        <a href="?sort=signature weapons&order=<?php echo $nextOrder; ?>">Signature Weapons</a>
    </p>
    <p>Currently sorted by <b><?php echo htmlspecialchars($sort); ?></b> (<?php echo $order; ?>)</p>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Character Rarity</th>
            <th>Vision</th>
            <th>Birthday</th>
            <th>Affiliation</th>
            <th>Nations</th>
            <th>Signature Weapons</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['character rarity']); ?></td>
            <td><?php echo htmlspecialchars($row['vision']); ?></td>
            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
            <td><?php echo htmlspecialchars($row['affiliation']); ?></td>
            <td><?php echo htmlspecialchars($row['nations']); ?></td>
            <td><?php echo htmlspecialchars($row['signature weapons']); ?></td>
            <td>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <a href="edit_character.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                    <a href="delete_character.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this character?');">Delete</a>
                <?php else: ?>
                    <span style="color:gray;">Edit</span> | <span style="color:gray;">Delete</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="index.php">Add New Character</a></p>
</body>
</html>