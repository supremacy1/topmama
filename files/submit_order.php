<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $product_id = $_POST['product_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $location_id = $_POST['location_id'];
    $order_date = date("Y-m-d H:i:s");
    $status = 'Pending';
    $transaction_id = uniqid("TRANS_");
    $total_amount = $price * $quantity;

    // Insert customer data
    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $transaction_id, $product_id, $quantity, $price);
    $stmt->execute();
    $stmt->close();

    // Insert order details
    $stmt = $conn->prepare("INSERT INTO order_details (transaction_id, customer_id, location_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siidss", $transaction_id, $customer_id, $location_id, $total_amount, $status, $order_date);
    $stmt->execute();
    $stmt->close();

    echo "success";
}
?>
