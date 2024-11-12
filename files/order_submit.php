<?php
include 'db_connection.php';

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $quantity = $_POST['quantity'];
    $location_id = $_POST['location_id'];
    $price = $_POST['price'];

    // Check if customer already exists by email
    $customerQuery = $conn->prepare("SELECT id FROM customers WHERE email = ?");
    $customerQuery->bind_param("s", $email);
    $customerQuery->execute();
    $customerResult = $customerQuery->get_result();
    
    if ($customerResult->num_rows > 0) {
        // Customer exists, get their ID
        $customer = $customerResult->fetch_assoc();
        $customer_id = $customer['id'];
    } else {
        // New customer, insert into customers table
        $insertCustomer = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
        $insertCustomer->bind_param("ssss", $name, $email, $phone, $address);
        $insertCustomer->execute();
        $customer_id = $conn->insert_id;
    }

    // Calculate total amount and generate transaction ID
    $total_amount = $quantity * $price;
    $transaction_id = uniqid("TRANS_");

    // Insert order details into orders table
    $insertOrder = $conn->prepare("INSERT INTO orders (transaction_id, customer_id, location_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $insertOrder->bind_param("siid", $transaction_id, $customer_id, $location_id, $total_amount);
    $insertOrder->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    $insertOrderItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $insertOrderItem->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    $insertOrderItem->execute();

    // Redirect back to the products page with a success message
    header("Location: products.php?success=1");
    exit();
}
?>
