<?php
// Include the database connection file
include 'db_connection.php';

// Process form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input from the request
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Insert customer details into the customers table
    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['name'], $data['email'], $data['phone'], $data['address']);
    $stmt->execute();
    $customerId = $stmt->insert_id; // Get the inserted customer ID

    // Insert order item into the order_item table
    $stmt = $conn->prepare("INSERT INTO order_items (product_id, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $data['product_id'], $data['quantity'], $data['price']);
    $stmt->execute();
    $orderId = $stmt->insert_id; // Assuming order_id is auto-increment

    // Generate order details
    $transactionId = uniqid(); // Generate a unique transaction ID
    $locationId = 1; // Assuming a default location ID
    $totalAmount = $data['price'] * $data['quantity']; // Calculate total amount
    $status = 'Pending'; // Set initial status
    $orderDate = date('Y-m-d H:i:s'); // Current date and time

    // Insert order details into the orders table
    $stmt = $conn->prepare("INSERT INTO orders (transaction_id, customer_id, location_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siidss", $transactionId, $customerId, $locationId, $totalAmount, $status, $orderDate);
    $stmt->execute();

    $message = 'Order placed successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Order Form</title>
    <link href="css/style.css" rel="stylesheet">


<link href="css/responsive.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Order Your Pizza</h1>
        <?php if ($message): ?>
            <div id="successMessage" class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <form id="orderForm" method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="product_id">Product ID:</label>
                <input type="number" class="form-control" id="product_id" name="product_id" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Orders</button>
        </form>
    </div>

    <script>
        document.getElementById('orderForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                address: formData.get('address'),
                product_id: formData.get('product_id'),
                quantity: formData.get('quantity'),
                price: formData.get('price'),
            };

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();
                document.getElementById('successMessage').innerText = result.message; // Display success message
                document.getElementById('successMessage').classList.add('alert', 'alert-success');
                this.reset(); // Reset the form after successful submission
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>
