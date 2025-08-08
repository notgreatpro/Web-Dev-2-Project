<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$search = $_GET['search'] ?? '';
$characters = getCharacters($pdo, $search, '', '');

?>
<div class="container">
    <h1>⚔️ Genshin Character Explorer</h1>
    <form method="get" class="filter-form" style="margin-bottom: 2em;">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit">Search</button>
    </form>
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