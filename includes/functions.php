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

// ----- Likes/Views Functions -----

// Increment view count for a character
function incrementCharacterViews($pdo, $character_id) {
    $stmt = $pdo->prepare("UPDATE characters SET views = views + 1 WHERE id = ?");
    return $stmt->execute([$character_id]);
}

// Increment like count for a character (use with duplicate-check)
function incrementCharacterLikes($pdo, $character_id) {
    $stmt = $pdo->prepare("UPDATE characters SET likes = likes + 1 WHERE id = ?");
    return $stmt->execute([$character_id]);
}

// Check if user already liked this character
function hasUserLikedCharacter($pdo, $user_id, $character_id) {
    $stmt = $pdo->prepare("SELECT id FROM character_likes WHERE user_id = ? AND character_id = ?");
    $stmt->execute([$user_id, $character_id]);
    return $stmt->fetch() ? true : false;
}

// Add a like record for this user/character
function addUserLikeCharacter($pdo, $user_id, $character_id) {
    $stmt = $pdo->prepare("INSERT INTO character_likes (user_id, character_id) VALUES (?, ?)");
    return $stmt->execute([$user_id, $character_id]);
}

function removeUserLikeCharacter($pdo, $user_id, $character_id) {
    $stmt = $pdo->prepare("DELETE FROM character_likes WHERE user_id = ? AND character_id = ?");
    return $stmt->execute([$user_id, $character_id]);
}
?>