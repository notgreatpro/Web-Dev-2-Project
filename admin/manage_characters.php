<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/header.php';

// Fetch all characters
$stmt = $pdo->query("SELECT * FROM characters ORDER BY name ASC");
$characters = $stmt->fetchAll();
?>

<div class="container">
    <h1>Manage Characters</h1>
    <a href="add_character.php" class="btn">Add New Character</a>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Vision</th>
                <th>Weapon</th>
                <th>Rarity</th>
                <th>Nation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($characters as $char): ?>
            <tr>
                <td><?= htmlspecialchars($char['name']) ?></td>
                <td><?= htmlspecialchars($char['vision']) ?></td>
                <td><?= htmlspecialchars($char['signature weapons']) ?></td>
                <td><?= htmlspecialchars($char['character rarity']) ?></td>
                <td><?= htmlspecialchars($char['nations']) ?></td>
                <td>
                    <a href="edit_character.php?id=<?= $char['id'] ?>">Edit</a> |
                    <a href="delete_character.php?id=<?= $char['id'] ?>" onclick="return confirm('Delete this character?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

<?php require_once '../includes/footer.php'; ?>