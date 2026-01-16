<?php
require 'config.php';

// Kontrollera admin-status
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$message = '';

// Lägg till produkt
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['product_name']);
    $description = sanitize($_POST['product_description']);
    $price = (float)$_POST['product_price'];
    $stock = (int)$_POST['product_stock'];
    $image_url = sanitize($_POST['product_image']) ?? 'images/placeholder.jpg';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $image_url, $_SESSION['user_id']]);
        $message = 'Produkt tillagd framgångsrikt!';
    } catch (PDOException $e) {
        $message = 'Fel vid tilläggning av produkt: ' . $e->getMessage();
    }
}

// Redigera produkt
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['product_id'];
    $name = sanitize($_POST['product_name']);
    $description = sanitize($_POST['product_description']);
    $price = (float)$_POST['product_price'];
    $stock = (int)$_POST['product_stock'];
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $stock, $id]);
    $message = 'Produkt uppdaterad!';
}

// Ta bort produkt
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Produkt borttagen!';
}

// Hämta alla produkter
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// Hämta produkt för redigering
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - WildHogs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main class="container admin-container">
        <h1>⚙️ Admin Panel</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <!-- Lägg till/Redigera Produkt -->
            <section class="admin-section">
                <h2><?php echo $edit_product ? 'Redigera Produkt' : 'Lägg till Ny Produkt'; ?></h2>
                <form method="POST" class="admin-form">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <input type="text" name="product_name" placeholder="Produktnamn" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
                    <textarea name="product_description" placeholder="Beskrivning" required><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    <input type="number" name="product_price" placeholder="Pris (kr)" step="0.01" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                    <input type="number" name="product_stock" placeholder="Lager" value="<?php echo $edit_product['stock'] ?? ''; ?>" required>
                    <input type="text" name="product_image" placeholder="Bild-URL" value="<?php echo $edit_product['image_url'] ?? ''; ?>">
                    
                    <button type="submit" name="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>" class="btn-primary">
                        <?php echo $edit_product ? 'Uppdatera Produkt' : 'Lägg till Produkt'; ?>
                    </button>
                    
                    <?php if ($edit_product): ?>
                        <a href="admin.php" class="btn-secondary">Avbryt</a>
                    <?php endif; ?>
                </form>
            </section>
            
            <!-- Produktlista -->
            <section class="admin-section">
                <h2>Produkter</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Namn</th>
                            <th>Pris</th>
                            <th>Lager</th>
                            <th>Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['price'], 2, ',', ' '); ?> kr</td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <a href="admin.php?edit=<?php echo $product['id']; ?>" class="btn-small">Redigera</a>
                                    <a href="admin.php?delete=<?php echo $product['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Är du säker?')">Radera</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
