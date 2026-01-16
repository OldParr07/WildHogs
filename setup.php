<?php
/**
 * WildHogs MC Klubb - Automatisk Setup
 * Detta script skapar databasen och admin-användare automatiskt
 * 
 * KÖR DENNA ENDAST EN GÅNG för att ställa upp systemet
 */

// Databaskonfiguration
$host = 'localhost';
$db_name = 'wildhogs';
$db_user = 'root';
$db_pass = '';

$setup_complete = false;
$messages = [];
$errors = [];

// Steg 1: Skapa anslutning till MySQL (utan databas först)
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $messages[] = "✅ Databasanslutning etablerad";
} catch (PDOException $e) {
    $errors[] = "❌ Kunde inte ansluta till MySQL: " . $e->getMessage();
    $errors[] = "Se till att MySQL är igång och att användarnamn/lösenord är korrekt i config.php";
}

// Steg 2: Skapa databasen
if (empty($errors)) {
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name;");
        $messages[] = "✅ Databas '$db_name' skapad/kontrollerad";
    } catch (PDOException $e) {
        $errors[] = "❌ Kunde inte skapa databas: " . $e->getMessage();
    }
}

// Steg 3: Anslut till den nya databasen
if (empty($errors)) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $messages[] = "✅ Ansluten till databasen '$db_name'";
    } catch (PDOException $e) {
        $errors[] = "❌ Kunde inte ansluta till databasen: " . $e->getMessage();
    }
}

// Steg 4: Skapa tabeller
if (empty($errors)) {
    $sql = "
    -- Användartabell
    CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Produkttabell
    CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        image_url VARCHAR(255),
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    );

    -- Ordertabell
    CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    -- Orderdetaljer
    CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    );
    ";

    // Dela upp SQL-kommandona
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    try {
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        $messages[] = "✅ Alla databastabeller skapade";
    } catch (PDOException $e) {
        $errors[] = "❌ Kunde inte skapa tabeller: " . $e->getMessage();
    }
}

// Steg 5: Lägg till testprodukter
if (empty($errors)) {
    try {
        // Kontrollera om produkter redan finns
        $check = $pdo->query("SELECT COUNT(*) FROM products");
        $count = $check->fetchColumn();
        
        if ($count == 0) {
            $pdo->exec("
                INSERT INTO products (name, description, price, stock, image_url) VALUES
                ('MC WildHogs T-shirt', 'Klassisk svart t-shirt med WildHogs logo', 199, 50, 'images/tshirt.jpg'),
                ('Läderkväll', 'Robust läderväst med MC-klubb märken', 1499, 20, 'images/vest.jpg'),
                ('Motorcykelhjälm', 'Motorcykelhjälm med WildHogs design', 799, 15, 'images/helmet.jpg'),
                ('MC Stövlar', 'Äkta läder motorcykelstövlar', 899, 25, 'images/boots.jpg');
            ");
            $messages[] = "✅ Testprodukter tillagda";
        } else {
            $messages[] = "ℹ️  Produkter finns redan (0 nya tillagda)";
        }
    } catch (PDOException $e) {
        $errors[] = "❌ Kunde inte lägga till testprodukter: " . $e->getMessage();
    }
}

// Steg 6: Skapa admin-användare
if (empty($errors)) {
    try {
        // Kontrollera om admin redan finns
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $check->execute(['admin']);
        $admin_exists = $check->fetchColumn();
        
        if (!$admin_exists) {
            $admin_password = password_hash('adminklubben123', PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', 'admin@wildhogs.se', $admin_password, 1]);
            $messages[] = "✅ Admin-användare skapad";
            $messages[] = "   Användarnamn: admin";
            $messages[] = "   Lösenord: adminklubben123";
            $messages[] = "   E-post: admin@wildhogs.se";
        } else {
            $messages[] = "ℹ️  Admin-användare finns redan";
        }
    } catch (PDOException $e) {
        $errors[] = "❌ Kunde inte skapa admin-användare: " . $e->getMessage();
    }
}

// Steg 7: Kopiera config.php om den inte finns
if (file_exists(__DIR__ . '/config.php')) {
    $messages[] = "ℹ️  config.php redan skapad";
} else {
    $config_content = '<?php
// Databasinställningar
$host = "localhost";
$db_name = "wildhogs";
$db_user = "root";
$db_pass = "";

// Skapa anslutning
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databasanslutning misslyckades: " . $e->getMessage());
}

// Säkerhet
session_start();
define("SESSION_TIMEOUT", 3600); // 1 timme

// Funktioner för autentisering
function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function isAdmin() {
    return isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1;
}

function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, "UTF-8");
}
?>';
    
    if (file_put_contents(__DIR__ . '/config.php', $config_content)) {
        $messages[] = "✅ config.php skapad";
    } else {
        $messages[] = "⚠️  Kunde inte skapa config.php (kanske redan finns)";
    }
}

$setup_complete = empty($errors);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildHogs - Automatisk Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a3a2e 0%, #2d5a4e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1a3a2e 0%, #3d7a66 100%);
            color: #d4af37;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 0.95rem;
            color: #ecf0f1;
        }
        .content {
            padding: 30px;
        }
        .message-box {
            margin-bottom: 20px;
        }
        .message {
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 0.95rem;
            border-left: 4px solid;
            font-family: 'Courier New', monospace;
        }
        .message.success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .message.info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .status {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .status-badge.success {
            background: #28a745;
            color: white;
        }
        .status-badge.error {
            background: #dc3545;
            color: white;
        }
        .next-steps {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .next-steps h3 {
            color: #1a3a2e;
            margin-bottom: 10px;
        }
        .next-steps ol {
            margin-left: 20px;
            color: #666;
            line-height: 1.8;
        }
        .next-steps li {
            margin-bottom: 8px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-primary {
            background: #e67e22;
            color: white;
        }
        .btn-primary:hover {
            background: #d35400;
        }
        .btn-secondary {
            background: #3d7a66;
            color: white;
        }
        .btn-secondary:hover {
            background: #2d5a4e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏍️ WildHogs MC Klubb</h1>
            <p>Automatisk installation</p>
        </div>
        
        <div class="content">
            <div class="message-box">
                <?php foreach ($messages as $msg): ?>
                    <div class="message success"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
                
                <?php foreach ($errors as $err): ?>
                    <div class="message error"><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
            </div>
            
            <div class="status">
                <?php if ($setup_complete): ?>
                    <div class="status-badge success">✅ Installation slutförd!</div>
                    
                    <div class="next-steps">
                        <h3>Nästa steg:</h3>
                        <ol>
                            <li>Skapa mappen <strong>images/</strong> för produktbilder</li>
                            <li>Gå till <strong>http://localhost/WildHogs</strong> för startsidan</li>
                            <li>Klicka <strong>"Logga In"</strong> i navbaren</li>
                            <li>Logga in som admin:
                                <ul style="margin-left: 20px; margin-top: 5px;">
                                    <li>Användarnamn: <strong>admin</strong></li>
                                    <li>Lösenord: <strong>adminklubben123</strong></li>
                                </ul>
                            </li>
                            <li>Klicka <strong>"Admin Panel"</strong> för att lägga till produkter</li>
                            <li><strong>Radera denna setup.php</strong> fil när du är klar</li>
                        </ol>
                    </div>
                    
                    <div class="button-group">
                        <a href="index.php" class="btn btn-primary">Gå till startsida</a>
                        <a href="login.php" class="btn btn-secondary">Gå till login</a>
                    </div>
                <?php else: ?>
                    <div class="status-badge error">❌ Installation misslyckades</div>
                    <p style="color: #dc3545; margin-top: 15px;">Se felmeddelandena ovan för detaljer</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
