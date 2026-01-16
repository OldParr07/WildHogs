<?php
require 'config.php';

$products = [];
$filter = '';

// Hämta alla produkter
$query = "SELECT * FROM products WHERE stock > 0";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll();

// Lägg till i varukorg
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'] ?? 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Rensa varukorg
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webbshop - WildHogs MC Klubb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main class="container">
        <section class="webshop-section">
            <h1>🏍️ WildHogs WebShop</h1>
            <p class="section-subtitle">Officiell merch och utrustning för MC-entusiaster</p>
            
            <?php if (isLoggedIn() && isAdmin()): ?>
                <div class="admin-panel">
                    <h3>Admin-panel</h3>
                    <a href="admin.php" class="btn-primary">Lägg till produkt</a>
                </div>
            <?php endif; ?>
            
            <div class="webshop-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                            <div class="product-footer">
                                <span class="price"><?php echo number_format($product['price'], 2, ',', ' '); ?> kr</span>
                                <span class="stock">Lager: <?php echo $product['stock']; ?></span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                <button type="submit" name="add_to_cart" class="btn-secondary">Lägg i varukorg</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-section">
                <h2>🛒 Din Varukorg</h2>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Produkt</th>
                                <th>Pris</th>
                                <th>Antal</th>
                                <th>Totalt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($_SESSION['cart'] as $product_id => $quantity):
                                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                                $stmt->execute([$product_id]);
                                $product = $stmt->fetch();
                                $item_total = $product['price'] * $quantity;
                                $total += $item_total;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?> kr</td>
                                    <td><?php echo $quantity; ?></td>
                                    <td><?php echo number_format($item_total, 2, ',', ' '); ?> kr</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="cart-total">
                        <h3>Totalt: <?php echo number_format($total, 2, ',', ' '); ?> kr</h3>
                    </div>
                    <div class="cart-actions">
                        <a href="webshop.php?clear_cart=1" class="btn-secondary">Rensa varukorg</a>
                        <?php if (isLoggedIn()): ?>
                            <form method="POST" action="checkout.php">
                                <button type="submit" class="btn-primary">Fortsätt till betalning</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn-primary">Logga in för att slutföra köp</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-cart">Din varukorg är tom</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
