<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: manage_news.php');
    exit;
}

// Fetch
$stmt = $pdo->prepare("SELECT * FROM news_updates WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "<div class='container'>News item not found. <a href='manage_news.php'>Back</a></div>";
    require_once '../includes/footer.php';
    exit;
}

$title = $item['title'];
$content = $item['content'];
$type = $item['type'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $type = in_array($_POST['type'] ?? '', ['genshin', 'website']) ? $_POST['type'] : 'genshin';

    if ($title === '') $errors[] = 'Title is required.';
    if ($content === '') $errors[] = 'Content is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE news_updates SET title = ?, content = ?, type = ? WHERE id = ?");
        $stmt->execute([$title, $content, $type, $id]);
        header('Location: manage_news.php');
        exit;
    }
}
?>

<div class="container" style="max-width:800px;">
    <h2>Edit News</h2>
    <?php if ($errors): ?>
        <div style="color:red;">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <table class="admin-form-table">
            <tr>
                <th>Title</th>
                <td><input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required></td>
            </tr>
            <tr>
                <th>Type</th>
                <td>
                    <select name="type" required>
                        <option value="genshin" <?= $type === 'genshin' ? 'selected' : '' ?>>Genshin Patch / News</option>
                        <option value="website" <?= $type === 'website' ? 'selected' : '' ?>>Website Update</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Content</th>
                <td><textarea name="content" rows="8" style="width:100%;" required><?= htmlspecialchars($content) ?></textarea></td>
            </tr>
        </table>

        <div class="admin-form-actions">
            <button type="submit" class="btn">Save Changes</button>
            <a href="manage_news.php" class="btn" style="background:#ccc;color:#222;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>