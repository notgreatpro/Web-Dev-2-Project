<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Genshin Character Info - Home</title>
</head>
<body>
    <h1>Welcome to Genshin Impact Character Info Database</h1>
    <p>
        <?php if ($isLoggedIn): ?>
            <a href="index.php">Add New Character</a> |
            <a href="characters_list.php">View Character List</a> |
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Log In</a> |
            <a href="character_list_public.php">Browse Characters</a>
        <?php endif; ?>
    </p>
    <hr>
    <p>This is your homepage. Use the links above to manage your Genshin Impact character database.</p>
</body>
</html>