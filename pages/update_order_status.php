<?php
include('../config/database.php');

$orderId = $_POST['order_id'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (!$orderId || !$newStatus) {
    echo "Order ID or status is missing!";
    exit;
}

// Update the order status
$query = "UPDATE orders SET status = :status WHERE id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['status' => $newStatus, 'order_id' => $orderId]);

// If the new status is "Delivered," set the customer status to "Inactive"
if ($newStatus === 'Delivered') {
    $query = "UPDATE customers 
              SET status = 'Inactive' 
              WHERE id = (SELECT customer_id FROM orders WHERE id = :order_id)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['order_id' => $orderId]);
}

echo "Order status updated successfully!";
?>
