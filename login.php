<?php
require 'config.php';

$error = '';
$success = '';

// Hantera login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Felaktigt användarnamn eller lösenord!';
    }
}

// Hantera registrering
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = sanitize($_POST['reg_username']);
    $email = sanitize($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $password_confirm = $_POST['reg_password_confirm'];
    
    if (strlen($password) < 6) {
        $error = 'Lösenordet måste vara minst 6 tecken långt!';
    } elseif ($password !== $password_confirm) {
        $error = 'Lösenorden matchar inte!';
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            $success = 'Registrering lyckades! Du kan nu logga in.';
        } catch (PDOException $e) {
            $error = 'Användarnamnet eller e-postadressen finns redan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logga In - WildHogs MC Klubb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>🏍️ WildHogs MC Klubb</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('login')">Logga In</button>
                <button class="tab-btn" onclick="showTab('register')">Registrera</button>
            </div>
            
            <!-- Login Tab -->
            <div id="login" class="tab-content active">
                <form method="POST">
                    <input type="text" name="username" placeholder="Användarnamn" required>
                    <input type="password" name="password" placeholder="Lösenord" required>
                    <button type="submit" name="login" class="btn-primary">Logga In</button>
                </form>
            </div>
            
            <!-- Register Tab -->
            <div id="register" class="tab-content">
                <form method="POST">
                    <input type="text" name="reg_username" placeholder="Användarnamn" required>
                    <input type="email" name="reg_email" placeholder="E-postadress" required>
                    <input type="password" name="reg_password" placeholder="Lösenord (min 6 tecken)" required>
                    <input type="password" name="reg_password_confirm" placeholder="Bekräfta lösenord" required>
                    <button type="submit" name="register" class="btn-primary">Registrera</button>
                </form>
            </div>
            
            <p class="back-link"><a href="index.php">← Tillbaka till startsida</a></p>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            const contents = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-btn');
            
            contents.forEach(content => content.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
