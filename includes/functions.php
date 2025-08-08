<?php
// Utility functions for fetching data

function getWeapons($pdo) {
    $stmt = $pdo->query("SELECT name FROM weapons");
    return $stmt->fetchAll();
}

function getNations($pdo) {
    $stmt = $pdo->query("SELECT name FROM nations");
    return $stmt->fetchAll();
}

function getCharacters($pdo, $search = '', $weapon = '', $nation = '') {
    $sql = "SELECT * FROM characters WHERE 1=1";
    $params = [];

    if ($search) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = "%$search%";
    }
    if ($weapon) {
        $sql .= " AND `signature weapons` = :weapon";
        $params[':weapon'] = $weapon;
    }
    if ($nation) {
        $sql .= " AND nations = :nation";
        $params[':nation'] = $nation;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>
<?php
// ...existing functions...

function getCharacterById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCommentsForCharacter($pdo, $character_id) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE character_id = ? ORDER BY created_at DESC");
    $stmt->execute([$character_id]);
    return $stmt->fetchAll();
}

// For adding a new comment (to be used in comment_submit.php)
function addComment($pdo, $character_id, $content) {
    $stmt = $pdo->prepare("INSERT INTO comments (character_id, content, created_at) VALUES (?, ?, NOW())");
    return $stmt->execute([$character_id, $content]);
}
?>