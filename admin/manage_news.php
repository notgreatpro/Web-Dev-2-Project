<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

// Fetch all news
$stmt = $pdo->query("SELECT * FROM news_updates ORDER BY created_at DESC");
$news_list = $stmt->fetchAll();
?>

<div class="container">
    <h1>Manage News & Updates</h1>
    <a href="add_news.php" class="btn">Add New</a>

    <table class="styled-table" style="margin-top:1em;">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($news_list as $n): ?>
                <tr>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($n['type'])) ?></td>
                    <td><?= htmlspecialchars($n['created_at']) ?></td>
                    <td>
                        <a href="edit_news.php?id=<?= $n['id'] ?>">Edit</a> |
                        <a href="delete_news.php?id=<?= $n['id'] ?>" onclick="return confirm('Delete this news item?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($news_list)): ?>
                <tr><td colspan="4">No news items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a class="btn" href="dashboard.php">Back to Dashboard</a>
</div>

<?php require_once '../includes/footer.php'; ?>