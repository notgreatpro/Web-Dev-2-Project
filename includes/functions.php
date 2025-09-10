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

function getCharacterById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Updated: join users to get username & avatar, and join characters to get character name!
function getUserCommentsWithCharacters($pdo, $user_id) {
    $stmt = $pdo->prepare(
        "SELECT c.content, c.created_at, ch.name AS character_name
         FROM comments c
         JOIN characters ch ON c.character_id = ch.id
         WHERE c.user_id = ?
         ORDER BY c.created_at DESC"
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// For adding a new comment (to be used in comment_submit.php)
function addComment($pdo, $character_id, $content, $user_id) {
    $stmt = $pdo->prepare("INSERT INTO comments (character_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$character_id, $user_id, $content]);
}

// For character page: get comments with user info
function getCommentsForCharacter($pdo, $character_id) {
    $stmt = $pdo->prepare(
        "SELECT c.content, c.created_at, u.username, u.avatar
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.character_id = ?
         ORDER BY c.created_at DESC"
    );
    $stmt->execute([$character_id]);
    return $stmt->fetchAll();
}
?>