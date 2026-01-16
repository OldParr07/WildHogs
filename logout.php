<?php
require 'config.php';

// Töm sessionen
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
?>
