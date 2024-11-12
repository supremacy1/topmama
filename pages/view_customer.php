<?php
include('../includes/header.php');
include('../config/database.php');
include('./functions.php'); // Include functions.php to use getOrderStats

// Get customer ID from URL
$customerId = $_GET['id'] ?? null;
if (!$customerId) {
    echo "Customer ID is missing!";
    exit;
}

// Fetch customer details
$query = "SELECT name, email FROM customers WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer not found!";
    exit;
}

// Fetch order statistics for the customer
$orderStats = getOrderStats($pdo, $customerId);

// Fetch individual orders with their statuses for this customer
$orderQuery = "SELECT id, status, order_date, total_amount FROM orders WHERE customer_id = :customer_id";
$orderStmt = $pdo->prepare($orderQuery);
$orderStmt->execute(['customer_id' => $customerId]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            color: #fff;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
        }
    </style>
    <title>Customer Details</title>
</head>
<body>
    <!-- Sidebar Section -->
    <div class="sidebar">
        <h3>Admin Dashboard</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_products.php">Manage Products</a>
        <a href="manage_orders.php">Manage Orders</a>
        <a href="manage_customers.php">Manage Customers</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <div class="container">
            <h2>Customer Details</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($customer['name']); ?></h5>
                    <p>Email: <?= htmlspecialchars($customer['email']); ?></p>
                </div>
            </div>

            <h4>Order Statistics</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Delivered</h5>
                            <p class="card-text"><?= $orderStats['Delivered'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Orders</h5>
                            <p class="card-text"><?= $orderStats['Pending'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Processed Orders</h5>
                            <p class="card-text"><?= $orderStats['Processed'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Cancelled Orders</h5>
                            <p class="card-text"><?= $orderStats['Cancelled'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display individual orders with statuses -->
            <h4 class="mt-4">Order Details</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']); ?></td>
                            <td><?= htmlspecialchars($order['status']); ?></td>
                            <td><?= htmlspecialchars($order['order_date']); ?></td>
                            <td>$<?= htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
