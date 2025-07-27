<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

// Sorting logic
$allowed = ['name', 'birthday', 'id'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT * FROM characters ORDER BY $sort $order";
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
        <a href="?sort=id&order=<?php echo $nextOrder; ?>">ID</a>
    </p>
    <p>Currently sorted by <b><?php echo htmlspecialchars($sort); ?></b> (<?php echo $order; ?>)</p>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Vision</th>
            <th>Birthday</th>
            <th>Affiliation</th>
            <th>Nation</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['vision']); ?></td>
            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
            <td><?php echo $row['affiliation_id']; ?></td>
            <td><?php echo $row['nation_id']; ?></td>
            <td>
                <a href="edit_character.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="delete_character.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this character?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="index.php">Add New Character</a></p>
</body>
</html>