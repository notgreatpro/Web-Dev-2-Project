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

// Increment views
incrementCharacterViews($pdo, $id);

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

// Fetch comments (includes username and avatar)
$comments = getCommentsForCharacter($pdo, $id);

// Fetch character timestamps 
$created_at = !empty($character['created_at']) ? date('Y-m-d H:i:s', strtotime($character['created_at'])) : 'Unknown';
$updated_at = !empty($character['updated_at']) ? date('Y-m-d H:i:s', strtotime($character['updated_at'])) : 'Never';

// Check if logged-in user already liked this character
$userLiked = false;
if (isset($_SESSION['user_logged_in'], $_SESSION['user_id']) && $_SESSION['user_logged_in'] === true) {
    $userLiked = hasUserLikedCharacter($pdo, $_SESSION['user_id'], $id);
}
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
            <div class="character-timestamps" style="margin-bottom:0;">
                <span style="color:#abd4d4;">Added: <b><?= $created_at ?></b></span><br>
                <span style="color:#abd4d4;">Last Edited: <b><?= $updated_at ?></b></span>
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

    <!-- Skills & Talents Section -->
    <?php if (
        !empty($character['normal_attack_name']) ||
        !empty($character['skill_name']) ||
        !empty($character['burst_name']) ||
        !empty($character['passive1_name']) ||
        !empty($character['passive2_name']) ||
        !empty($character['utility_passive_name'])
    ): ?>
        <div class="character-skills">
            <h2>Skills & Talents</h2>
            <?php if (!empty($character['normal_attack_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        Normal Attack: <?= htmlspecialchars($character['normal_attack_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['normal_attack_description'])) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($character['skill_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        Elemental Skill: <?= htmlspecialchars($character['skill_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['skill_description'])) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($character['burst_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        Elemental Burst: <?= htmlspecialchars($character['burst_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['burst_description'])) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($character['passive1_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        1st Ascension Passive: <?= htmlspecialchars($character['passive1_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['passive1_description'])) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($character['passive2_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        4th Ascension Passive: <?= htmlspecialchars($character['passive2_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['passive2_description'])) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($character['utility_passive_name'])): ?>
                <div class="talent-block">
                    <h3 style="color:#4c398d;">
                        Utility Passive: <?= htmlspecialchars($character['utility_passive_name']) ?>
                    </h3>
                    <p><?= nl2br(htmlspecialchars($character['utility_passive_description'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Likes & Views at the bottom of Skills/Talents -->
            <div class="character-likes-views" style="margin-top:32px;">
                <span class="cv-label"><b>Likes:</b> <?= intval($character['likes']) ?></span><br>
                <span class="cv-label"><b>Views:</b> <?= intval($character['views']) ?></span>
                <?php if (isset($_SESSION['user_logged_in'], $_SESSION['user_id']) && $_SESSION['user_logged_in'] === true): ?>
                    <?php if ($userLiked): ?>
                        <form method="post" action="unlike_character.php" style="margin-top:10px;">
                            <input type="hidden" name="character_id" value="<?= $id ?>">
                            <button type="submit" class="like-btn">👎 Unlike</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="like_character.php" style="margin-top:10px;">
                            <input type="hidden" name="character_id" value="<?= $id ?>">
                            <button type="submit" class="like-btn">👍 Like</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <hr>
    <div class="character-comments">
        <h2>Comments</h2>
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <?php
                $avatarFile = (!empty($comment['avatar']) && file_exists(__DIR__ . "/../public/img/" . $comment['avatar']))
                    ? "/public/img/" . htmlspecialchars($comment['avatar'])
                    : "/public/img/default_avatar.png";
                ?>
                <div class="comment-row">
                    <div class="comment-meta">
                        <img class="comment-avatar"
                             src="<?= $avatarFile ?>"
                             alt="<?= htmlspecialchars($comment['username'] ?? 'User') ?>'s avatar"
                             onerror="this.onerror=null;this.src='/public/img/default_avatar.png';">
                        <span class="comment-username"><?= htmlspecialchars($comment['username'] ?? 'Unknown') ?></span>
                        <span class="comment-date"><?= htmlspecialchars($comment['created_at']) ?></span>
                    </div>
                    <div class="comment-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
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