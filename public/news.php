<?php
// news.php placed in public/ — adjust include paths relative to this file
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Optional: filter by type via GET: ?type=genshin or ?type=website
$allowed_types = ['genshin', 'website'];
$type = isset($_GET['type']) && in_array($_GET['type'], $allowed_types) ? $_GET['type'] : '';

$sql = "SELECT * FROM news_updates WHERE 1=1";
$params = [];
if ($type) {
    $sql .= " AND type = :type";
    $params[':type'] = $type;
}
$sql .= " ORDER BY created_at DESC LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/news.css">

<div class="container">
    <h1>Latest News & Updates</h1>

    <div style="margin: 12px 0;">
        <a href="news.php" class="btn" style="margin-right:8px; <?= $type === '' ? 'opacity:0.9;' : '' ?>">All</a>
        <a href="news.php?type=genshin" class="btn" style="margin-right:8px; <?= $type === 'genshin' ? 'opacity:0.9;' : '' ?>">Genshin</a>
        <a href="news.php?type=website" class="btn" style="<?= $type === 'website' ? 'opacity:0.9;' : '' ?>">Website</a>
    </div>

    <div class="news-list">
        <?php if (empty($news)): ?>
            <p>No updates found.</p>
        <?php else: ?>
            <?php foreach ($news as $n): ?>
                <article class="news-item" id="item-<?= htmlspecialchars($n['id']) ?>">
                    <header>
                        <h3><?= htmlspecialchars($n['title']) ?></h3>
                        <div class="news-meta">
                            <span class="news-type"><?= htmlspecialchars(ucfirst($n['type'])) ?></span>
                            <span class="news-date"><?= htmlspecialchars($n['created_at']) ?></span>
                        </div>
                    </header>
                    <div class="news-body">
                        <?= nl2br(htmlspecialchars($n['content'])) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>