<?php
session_start();
// Redirect logged-in users to admin character list page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: characters_list.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "genshin_character_info_database");

// Sorting logic
$allowed = [
    'name',
    'birthday',
    'id',
    'affiliation',
    'vision',
    'nations',
    'signature weapons'
];
$sort = (isset($_GET['sort']) && in_array($_GET['sort'], $allowed)) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT * FROM characters ORDER BY `$sort` $order";
$result = $conn->query($sql);

// For toggling sort order in links
$nextorder = $order === 'ASC' ? 'desc' : 'asc';

// Helper for sort links
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
    <title>Character List (Public)</title>
</head>
<body>
    <h1>Character List (Public)</h1>
    <table border="1" cellpadding="5">
        <tr>
            <th><?php echo sort_link('name', 'Name', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('character rarity', 'Rarity', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('vision', 'Vision', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('birthday', 'Birthday', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('affiliation', 'Affiliation', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('nations', 'Nations', $sort, $order, $nextorder); ?></th>
            <th><?php echo sort_link('signature weapons', 'Signature Weapons', $sort, $order, $nextorder); ?></th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <a href="character_detail_public.php?id=<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['character rarity']); ?></td>
            <td><?php echo htmlspecialchars($row['vision']); ?></td>
            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
            <td><?php echo htmlspecialchars($row['affiliation']); ?></td>
            <td><?php echo htmlspecialchars($row['nations']); ?></td>
            <td><?php echo htmlspecialchars($row['signature weapons']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="login.php">Admin Login</a>
</body>
</html>