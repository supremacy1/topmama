<?php
// Include the database connection
include 'db_connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $product_id = $_POST['product_id'];
    $price = $_POST['price'];
    $quantity = 1; // Assuming quantity is 1 for simplicity
    $status = 'Pending';
    $order_date = date("Y-m-d H:i:s");

    // Insert customer information
    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    // Generate unique transaction ID
    $transaction_id = uniqid("TRANS_");

    // Insert order information
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $transaction_id, $product_id, $quantity, $price);
    $stmt->execute();
    $stmt->close();

    // Insert order details
    $total_amount = $quantity * $price;
    $location_id = 1; // Assuming a fixed location for simplicity

    $stmt = $conn->prepare("INSERT INTO order_details (transaction_id, customer_id, location_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siidss", $transaction_id, $customer_id, $location_id, $total_amount, $status, $order_date);
    $stmt->execute();
    $stmt->close();

    // Redirect or display success message
    echo "Order placed successfully!";
}
?>
