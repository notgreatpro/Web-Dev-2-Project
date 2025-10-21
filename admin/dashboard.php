<?php
require_once '../includes/auth.php';
require_once '../includes/admin_header.php';
require_once '../config/db.php';

// Fetch latest 3 news items for quick admin preview
$latestNews = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, type, created_at FROM news_updates ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $latestNews = $stmt->fetchAll();
} catch (Exception $e) {
    // silently ignore if table doesn't exist yet
    $latestNews = [];
}
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div style="display: flex; gap: 2em; flex-wrap: wrap; margin-top:2em;">
        <div style="background:#61a3a6; color:#050706; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #15dede22;">
            <h2 style="color:#050706; margin-top:0;">Characters</h2>
            <p>View, add, edit, or remove Genshin Impact characters in the database.</p>
            <a class="btn" href="manage_characters.php">Manage Characters</a>
        </div>

        <div style="background:#61a3a6; color:#050706; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #15dede22;">
            <h2 style="color:#050706; margin-top:0;">Comments</h2>
            <p>Review and moderate user comments on characters.</p>
            <a class="btn" href="manage_comments.php">Manage Comments</a>
        </div>

        <div style="background:#61a3a6; color:#050706; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #15dede22;">
            <h2 style="color:#050706; margin-top:0;">Settings</h2>
            <p>Modify admin settings and log out securely.</p>
            <a class="btn" href="/public/index.php">Log Out</a>
        </div>

        <!-- News admin card -->
        <div style="background:#61a3a6; color:#050706; border-radius:14px; padding:1.4em 2.2em; flex:1 1 420px; min-width:320px; box-shadow:0 2px 10px #bfa94b33;">
            <h2 style="color:#050706; margin-top:0;">News & Updates</h2>
            <p style="margin-bottom:12px;">Quick preview of latest news. Manage all posts below.</p>

            <?php if (empty($latestNews)): ?>
                <div style="background:#fff; border-radius:8px; padding:10px; color:#666;">
                    No news items found.
                </div>
            <?php else: ?>
                <ul style="list-style:none; padding:0; margin:0 0 10px 0;">
                    <?php foreach ($latestNews as $n): ?>
                        <li style="padding:8px 0; border-bottom:1px dashed #f0dcae;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
                                <div>
                                    <strong style="display:block;"><?= htmlspecialchars($n['title']) ?></strong>
                                    <small style="color:#85b4c0;"><?= htmlspecialchars(ucfirst($n['type'])) ?> • <?= htmlspecialchars($n['created_at']) ?></small>
                                </div>
                                <div style="white-space:nowrap;">
                                    <a href="edit_news.php?id=<?= $n['id'] ?>" style="margin-right:8px;">Edit</a>
                                    <a href="delete_news.php?id=<?= $n['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');" style="color:#b33;">Delete</a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div style="margin-top:12px;">
                <a class="btn" href="manage_news.php" style="background:#1db5c0; color:#fff; border-radius:8px; padding:8px 12px;">Manage News</a>
                <a class="btn" href="add_news.php" style="background:#1db5c0; color:#fff; border:1px solid #1db5c0; margin-left:8px; padding:8px 12px; border-radius:8px;">Add News</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>