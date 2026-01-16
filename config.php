<?php
// Databasinställningar
$host = 'localhost';
$db_name = 'wildhogs';
$db_user = 'root';
$db_pass = '';

// Skapa anslutning
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databasanslutning misslyckades: " . $e->getMessage());
}

// Säkerhet
session_start();
define('SESSION_TIMEOUT', 3600); // 1 timme

// Funktioner för autentisering
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
?>
