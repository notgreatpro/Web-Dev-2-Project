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

// Fetch latest news updates to show on the homepage (small summary, up to 5)
try {
    $newsStmt = $pdo->prepare("SELECT id, title, content, type, created_at FROM news_updates ORDER BY created_at DESC LIMIT 5");
    $newsStmt->execute();
    $news_items = $newsStmt->fetchAll();
} catch (Exception $e) {
    // If the table doesn't exist or another error happens, silently degrade (no news shown)
    $news_items = [];
}
?>
<link rel="stylesheet" href="css/sort-bar.css">
<link rel="stylesheet" href="css/news.css">

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

    <!-- Embedded News & Updates section added to the homepage -->
    <div class="news-section" style="margin-top: 2.6em;">
        <h2 style="margin-bottom: 0.6em;">Latest News & Updates</h2>

        <?php if (empty($news_items)): ?>
            <div class="news-list">
                <p>No updates available right now. <a href="news.php">View all updates</a></p>
            </div>
        <?php else: ?>
            <div class="news-list">
                <?php foreach ($news_items as $n): ?>
                    <article class="news-item">
                        <header>
                            <h3 style="margin-bottom:6px;"><?= htmlspecialchars($n['title']) ?></h3>
                            <div class="news-meta" style="margin-bottom:8px;">
                                <span class="news-type"><?= htmlspecialchars(ucfirst($n['type'])) ?></span>
                                <span class="news-date" style="margin-left:8px;"><?= htmlspecialchars($n['created_at']) ?></span>
                            </div>
                        </header>
                        <div class="news-body" style="color:#333;">
                            <?php
                                // Show a short excerpt (first ~220 characters) for the homepage
                                $plain = strip_tags($n['content']);
                                $excerpt = mb_strlen($plain) > 220 ? mb_substr($plain, 0, 220) . '…' : $plain;
                                echo nl2br(htmlspecialchars($excerpt));
                            ?>
                        </div>
                        <div style="margin-top:10px;">
                            <a href="news.php#item-<?= $n['id'] ?>" class="btn" style="padding:6px 12px; font-size:0.92rem;">Read more</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:12px;">
                <a href="news.php" class="btn">View all updates</a>
            </div>
        <?php endif; ?>
    </div>
    <!-- End of News section -->

</div>
<?php require_once '../includes/footer.php'; ?>