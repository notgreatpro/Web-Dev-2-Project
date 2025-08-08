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