<?php
session_start();
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
$imageFile = $character['image'] ?: "default.png";
$imgPath = __DIR__ . "/img/" . $imageFile;
$img = file_exists($imgPath) ? "img/" . $imageFile : "img/default.png";

// Fetch comments
$comments = getCommentsForCharacter($pdo, $id);

// Fetch character timestamps 
$created_at = !empty($character['created_at']) ? date('Y-m-d H:i:s', strtotime($character['created_at'])) : 'Unknown';
$updated_at = !empty($character['updated_at']) ? date('Y-m-d H:i:s', strtotime($character['updated_at'])) : 'Never';
?>

<link rel="stylesheet" href="character-detail.css">

<div class="container">
    <div class="character-detail">
        <!-- Left column: image, stars, quote, timestamps -->
        <div class="character-left-col">
            <div>
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($character['name']) ?>" class="character-img-detail">
                <span class="character-stars"><?= str_repeat('⭐', $character['character rarity']) ?></span>
            </div>
            <?php if (!empty($character['quote'])): ?>
                <div class="character-quote">
                    "<?= htmlspecialchars($character['quote']) ?>"
                </div>
            <?php endif; ?>
            <div class="character-timestamps">
                <span>Added: <b><?= $created_at ?></b></span><br>
                <span>Last Edited: <b><?= $updated_at ?></b></span>
            </div>
        </div>
        <!-- Middle column: name and stats -->
        <div class="character-middle-col">
            <div class="character-title"><?= htmlspecialchars($character['name']) ?></div>
            <ul class="character-info-list">
                <li><b>Vision:</b> <?= htmlspecialchars($character['vision']) ?></li>
                <li><b>Signature Weapon:</b> <?= htmlspecialchars($character['signature weapons']) ?></li>
                <li><b>Nation:</b> <?= htmlspecialchars($character['nations']) ?></li>
                <li><b>Affiliation:</b> <?= htmlspecialchars($character['affiliation']) ?></li>
                <li><b>Birthday:</b> <?= htmlspecialchars($character['birthday']) ?></li>
            </ul>
        </div>
        <!-- Right column: description -->
        <div class="character-right-col">
            <div class="character-desc">
                <b>Description:</b>
                <p><?= nl2br(htmlspecialchars($character['description'])) ?></p>
            </div>
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
            <p>There are no comments Traveller. Be sure to be first to comment your favourite characters!</p>
        <?php endif; ?>
        <hr>
        <h3>Leave a Comment Traveller!</h3>
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
            <form method="post" action="comment_submit.php">
                <input type="hidden" name="character_id" value="<?= $id ?>">
                <textarea name="content" rows="4" cols="60" required placeholder="Your comment..."></textarea><br>
                <button type="submit">Submit</button>
            </form>
        <?php else: ?>
            <p style="color:#b00;">You must <a href="user_login.php">login</a> to comment!</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>