<?php
// fetch_products.php

header('Content-Type: application/json');


// Database connection
$host = 'localhost';
$dbname = 'pizza_chops_shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Fetch products with images
$sql = "SELECT  product.id, product.name, product.description
        FROM product 
        JOIN product_ ON product.id = product_image.product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output as JSON
echo json_encode($products);
