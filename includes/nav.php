<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php">🏍️ WildHogs MC</a>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Hem</a></li>
            <li><a href="webshop.php">Webbshop</a></li>
            <?php if (isLoggedIn()): ?>
                <li><span class="welcome-text">Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <?php if (isAdmin()): ?>
                    <li><a href="admin.php" class="nav-admin">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn-logout">Logga Ut</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Logga In</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>