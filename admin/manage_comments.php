<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/admin_header.php';

$stmt = $pdo->query(
  "SELECT comments.*, characters.name AS character_name 
   FROM comments 
   JOIN characters ON comments.character_id = characters.id 
   ORDER BY comments.created_at DESC"
);
$comments = $stmt->fetchAll();
?>
<div class="container">
    <h1>Manage Comments</h1>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Character</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['character_name']) ?></td>
                <td><?= nl2br(htmlspecialchars($c['content'])) ?></td>
                <td><?= htmlspecialchars($c['created_at']) ?></td>
                <td>
                    <a href="delete_comments.php?id=<?= $c['id'] ?>" onclick="return confirm('Delete this comment?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a class="btn" href="dashboard.php">Back to Dashboard</a>
</div>
<?php require_once '../includes/footer.php'; ?>