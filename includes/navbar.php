<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <ul class="navbar">
        <li class="logo">
            <a href=""><img src="/public/img/Teyvat_Archives_Logo.png" alt="Logo" class="nav-logo"></a>
            <span class="site-title">Teyvat Archives</span>
        </li>
        <li><a href="index.php">Genshin Characters</a></li>
        <li><a href="/public/feedback.php">Feedback</a></li>
        <li><a href="/public/faq.php">FAQ</a></li>
        <li><a href="/public/rules.php">Rules & Guidelines</a></li>
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="user_logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="user_login.php">User Login</a></li>
            <li><a href="sign_up.php">Sign Up</a></li>
        <?php endif; ?>
        <li class="right"><a href="adminlogin.php">Admin Login</a></li>
    </ul>
</nav>