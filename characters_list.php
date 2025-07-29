<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

// Sorting logic
$allowed = [
    'name',
    'character rarity',
    'vision',
    'birthday',
    'affiliation',
    'nations',
    'signature weapons'
];
$sort = (isset($_GET['sort']) && in_array($_GET['sort'], $allowed)) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT * FROM characters ORDER BY `$sort` $order";
$result = $conn->query($sql);

// For toggling sort order in links
$nextorder = $order === 'ASC' ? 'desc' : 'asc';

// Helper function for sort links
function sort_link($column, $label, $current_sort, $current_order, $nextorder) {
    $arrow = '';
    if ($current_sort === $column) {
        $arrow = $current_order === 'ASC' ? ' ▲' : ' ▼';
    }
    return '<a href="?sort=' . urlencode($column) . '&order=' . $nextorder . '">' . htmlspecialchars($label) . $arrow . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Genshin Character List (Admin)</title>
    <style>
        a { color: purple; }
        th { text-align: left; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px 10px; }
    </style>
</head>
<body>
    <h1>Genshin Character List (Admin)</h1>
    <table>
        <tr>
            <th><?php echo sort_link('name', 'Name', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('character rarity', 'Rarity', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('vision', 'Vision', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('birthday', 'Birthday', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('affiliation', 'Affiliation', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('nations', 'Nations', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('signature weapons', 'Signature Weapons', $sort, $order, $nextorder); ?></th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <a href="character_detail.php?id=<?php echo (int)$row['id']; ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['character rarity']); ?></td>
            <td><?php echo htmlspecialchars($row['vision']); ?></td>
            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
            <td><?php echo htmlspecialchars($row['affiliation']); ?></td>
            <td><?php echo htmlspecialchars($row['nations']); ?></td>
            <td><?php echo htmlspecialchars($row['signature weapons']); ?></td>
            <td><a href="edit_character.php?id=<?php echo (int)$row['id']; ?>">Edit</a></td>
            <td><a href="delete_character.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Are you sure you want to delete this character?');">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="index.php">Add New Character</a> |
    <a href="logout.php">Logout</a> |
    <a href="home.php">Return to Home</a>
</body>
</html>