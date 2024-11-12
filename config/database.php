<?php
// Database credentials
$host = 'localhost';
$dbname = 'pizza_chops_shop';
$user = 'root';
$password = '';

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
