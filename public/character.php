<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Validate and sanitize id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<p>Invalid character ID.</p>";
    require_once '../includes/footer.php';
    exit;
}

// Fetch character data
$character = getCharacterById($pdo, $id);
if (!$character) {
    echo "<p>Character not found.</p>";
    require_once '../includes/footer.php';
    exit;
}

// Prepare image
$img = "img/" . preg_replace('/[^a-zA-Z0-9]/', '', $character['name']) . ".jpg";
if (!file_exists($img)) {
    $img = "img/default.png";
}

// Fetch comments
$comments = getCommentsForCharacter($pdo, $id);
?>

<div class="container">
    <div class="character-detail">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($character['name']) ?>" class="character-img-detail">
        <h1><?= htmlspecialchars($character['name']) ?> <?= str_repeat('⭐', $character['character rarity']) ?></h1>
        <ul class="character-info-list">
            <li><b>Vision:</b> <?= htmlspecialchars($character['vision']) ?></li>
            <li><b>Weapon:</b> <?= htmlspecialchars($character['signature weapons']) ?></li>
            <li><b>Nation:</b> <?= htmlspecialchars($character['nations']) ?></li>
            <li><b>Affiliation:</b> <?= htmlspecialchars($character['affiliation']) ?></li>
            <li><b>Birthday:</b> <?= htmlspecialchars($character['birthday']) ?></li>
        </ul>
        <div class="character-desc">
            <b>Description:</b>
            <p><?= nl2br(htmlspecialchars($character['description'])) ?></p>
        </div>
    </div>

    <hr>
    <div class="character-comments">
        <h2>Comments</h2>
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <span class="comment-date"><?= htmlspecialchars($comment['created_at']) ?></span>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
        <hr>
        <h3>Leave a Comment</h3>
        <form method="post" action="comment_submit.php">
            <input type="hidden" name="character_id" value="<?= $id ?>">
            <textarea name="content" rows="4" cols="60" required placeholder="Your comment..."></textarea><br>
            <!-- CAPTCHA will go here in the next step -->
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>