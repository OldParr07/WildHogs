<?php
require 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: webshop.php');
    exit;
}

$message = '';

// Slutför order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_order'])) {
    $total = 0;
    
    // Beräkna total
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        $total += $product['price'] * $quantity;
    }
    
    try {
        // Skapa order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();
        
        // Lägg till order items
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
            
            // Uppdatera lager
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);
        }
        
        $_SESSION['cart'] = [];
        $message = 'Beställning slutförd! Tack för ditt köp.';
        header('Refresh: 2; url=index.php');
    } catch (PDOException $e) {
        $message = 'Fel vid beställning: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kassa - WildHogs MC Klubb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main class="container">
        <section class="checkout-section">
            <h1>🛒 Kassa</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="checkout-content">
                <div class="order-summary">
                    <h2>Orderöversikt</h2>
                    <table class="order-table">
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
                    <div class="order-total">
                        <h3>Totalt att betala: <?php echo number_format($total, 2, ',', ' '); ?> kr</h3>
                    </div>
                </div>
                
                <div class="order-form">
                    <h2>Leveransadress</h2>
                    <form method="POST" class="delivery-form">
                        <label>Namn:</label>
                        <input type="text" name="full_name" required>
                        
                        <label>Adress:</label>
                        <input type="text" name="address" required>
                        
                        <label>Postnummer:</label>
                        <input type="text" name="postal_code" required>
                        
                        <label>Stad:</label>
                        <input type="text" name="city" required>
                        
                        <label>E-postadress:</label>
                        <input type="email" name="email" value="<?php echo $_SESSION['username']; ?>" required>
                        
                        <button type="submit" name="complete_order" class="btn-primary">Bekräfta Beställning</button>
                        <a href="webshop.php" class="btn-secondary">Tillbaka till varukorg</a>
                    </form>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
