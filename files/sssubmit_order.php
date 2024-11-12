<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $product_id = $_POST['product_id'];
    $normal_price = $_POST['normal_price'];
    $quantity = 1;  // Set a default quantity for simplicity

    // Insert into customer table
    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    // Insert into order_item table
    $stmt = $conn->prepare("INSERT INTO order_item (product_id, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $product_id, $quantity, $price);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Calculate total amount and create order in order_details
    $total_amount = $quantity * $price;
    $location_id = 1;  // Assuming default location
    $status = "pending";

    $stmt = $conn->prepare("INSERT INTO order_details (customer_id, location_id, total_amount, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $customer_id, $location_id, $total_amount, $status);
    $stmt->execute();
    $transaction_id = $stmt->insert_id;
    $stmt->close();

    echo "Order submitted successfully with Transaction ID: " . $transaction_id;
} else {
    echo "Invalid request.";
}

$conn->close();
?>
