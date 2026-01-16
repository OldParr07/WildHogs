<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<footer class="footer">
    <div class="container footer-content">
        <div class="footer-section">
            <h3>Om WildHogs</h3>
            <p>Vi är en MC-klubb dedikerad till motorcyklar, vänskap och vildsvin-traditionen.</p>
        </div>
        <div class="footer-section">
            <h3>Snabblänkar</h3>
            <ul>
                <li><a href="index.php">Hem</a></li>
                <li><a href="webshop.php">Webbshop</a></li>
                <li><a href="webshop.php">Produkter</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Konto</h3>
            <ul>
                <?php if (isLoggedIn()): ?>
                    <li><span>Inloggad som: <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="logout.php">Logga Ut</a></li>
                <?php else: ?>
                    <li><a href="login.php">Logga In / Registrera</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Kontakt</h3>
            <p>📧 info@wildhogs.se</p>
            <p>📱 070-123 45 67</p>
            <p>📍 Värmland, Sverige</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 WildHogs MC Klubb. Alla rättigheter förbehållna. 🏍️</p>
    </div>
</footer>