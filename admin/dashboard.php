<?php
require_once '../includes/auth.php';
require_once '../includes/admin_header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div style="display: flex; gap: 2em; flex-wrap: wrap; margin-top:2em;">
        <div style="background:#23233b; color:#ffe066; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #23233b22;">
            <h2 style="color:#ffe066; margin-top:0;">Characters</h2>
            <p>View, add, edit, or remove Genshin Impact characters in the database.</p>
            <a class="btn" href="manage_characters.php">Manage Characters</a>
        </div>
        <div style="background:#23233b; color:#ffe066; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #23233b22;">
            <h2 style="color:#ffe066; margin-top:0;">Comments</h2>
            <p>Review and moderate user comments on characters.</p>
            <a class="btn" href="manage_comments.php">Manage Comments</a>
        </div>
        <div style="background:#fff; color:#23233b; border-radius:14px; padding:1.7em 2.6em; flex:1; min-width:220px; box-shadow:0 2px 10px #23233b22;">
            <h2 style="color:#383870; margin-top:0;">Settings</h2>
            <p>Modify admin settings and log out securely.</p>
            <a class="btn" href="/public/index.php" style="background:#ffe066; color:#23233b;">Log Out</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>