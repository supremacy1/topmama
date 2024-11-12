<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get posted data
    $locationId = $_POST['location_id']; // Store location ID
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $totalAmount = $quantity * $price;
    $status = "pending";
    $orderDate = date("Y-m-d H:i:s");

    // Check if the customer already exists based on email
    $checkCustomerQuery = "SELECT id FROM customers WHERE email = ?";
    $stmt = $conn->prepare($checkCustomerQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Customer already exists, retrieve the customer ID
        $customer = $result->fetch_assoc();
        $customerId = $customer['id'];
    } else {
        // Insert new customer
        $insertCustomerQuery = "INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertCustomerQuery);
        $stmt->bind_param("ssss", $name, $email, $phone, $address);
        $stmt->execute();
        $customerId = $conn->insert_id;
    }
    
    // Generate a unique transaction ID
    // $transactionId = uniqid('txn_');
    $transactionId = 'topmama_' . str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Insert the order with location_id
    $insertOrderQuery = "INSERT INTO orders (transaction_id, customer_id, location_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertOrderQuery);
    $stmt->bind_param("siidss", $transactionId, $customerId, $locationId, $totalAmount, $status, $orderDate);
    $stmt->execute();
    $orderId = $conn->insert_id;

    // Insert into order_items table
    $insertOrderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertOrderItemQuery);
    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
    $stmt->execute();

    echo "Order placed successfully!";
    
    
} else {
    echo "Invalid request.";
}
?>
