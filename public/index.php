<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Get search and sorting parameters
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? 'name';
$order  = $_GET['order'] ?? 'asc';

// Whitelist for sorting columns and directions
$sortable = [
    'name' => 'Name',
    'created_at' => 'Created',
    'updated_at' => 'Updated',
];
$order = strtolower($order) === 'desc' ? 'desc' : 'asc';

$sort_column = isset($sortable[$sort]) ? $sort : 'name';

// Build the SQL query with sorting
$sql = "SELECT * FROM characters WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND name LIKE :search";
    $params[':search'] = "%$search%";
}
$sql .= " ORDER BY $sort_column $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$characters = $stmt->fetchAll();

function next_order($current_order) {
    return $current_order === 'asc' ? 'desc' : 'asc';
}
?>
<link rel="stylesheet" href="css/sort-bar.css">
<div class="container">
    <h1>Genshin Character Explorer</h1>
    <form method="get" class="filter-form" style="margin-bottom: 2em;">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search ?? '') ?>">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_column) ?>">
        <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
        <button type="submit">Search</button>
    </form>
    <div class="sort-bar">
        <span class="sort-bar-label">Sort by:</span>
        <?php foreach ($sortable as $col => $label): ?>
            <?php
                $active = ($sort_column === $col);
                $new_order = $active ? next_order($order) : 'asc';
                $arrow = $active ? '<span class="sort-bar-arrow">'.($order === 'asc' ? '↑' : '↓').'</span>' : '';
            ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $col ?>&order=<?= $new_order ?>"
               class="sort-bar-link<?= $active ? ' active' : '' ?>">
                <?= $label ?><?= $arrow ?>
            </a>
        <?php endforeach; ?>
        <span class="sort-bar-current">(<?= $sortable[$sort_column] ?>, <?= strtoupper($order) ?>)</span>
    </div>
    <div class="character-list">
        <?php foreach ($characters as $c):
            $imgSrc = (isset($c['image']) && $c['image'] && file_exists(__DIR__ . "/img/" . $c['image']))
                ? "img/" . $c['image']
                : "img/default.png";
        ?>
            <div class="character-card">
                <a href="character.php?id=<?= $c['id'] ?>">
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($c['name']) ?>" class="character-img">
                    <h3><?= htmlspecialchars($c['name']) ?></h3>
                    <p><b>Weapon:</b> <?= htmlspecialchars($c['signature weapons']) ?></p>
                    <p><b>Nation:</b> <?= htmlspecialchars($c['nations']) ?></p>
                    <p><b>Rarity:</b> <?= str_repeat('⭐', $c['character rarity']) ?></p>
                </a>
            </div>
        <?php endforeach; ?>
        <?php if (empty($characters)): ?>
            <p>No characters found.</p>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>