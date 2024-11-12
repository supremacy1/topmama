<?php
include('../includes/header.php');
include('../config/database.php');

// Get order ID from URL
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    echo "Order ID is missing.";
    exit;
}

// Fetch order details
$query = "SELECT o.*, c.name as customer_name, c.phone, c.email, c.address, s.location_name, s.delivery_fee 
          FROM orders o
          JOIN customers c ON o.customer_id = c.id
          JOIN store_locations s ON o.location_id = s.id
          WHERE o.id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch items in the order
$query = "SELECT oi.quantity, oi.price, p.name as product_name
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update order status based on action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'confirm_payment':
            $new_status = 'Processed';
            break;
        case 'process_order':
            $new_status = 'Processed';
            break;
        case 'deliver_order':
            $new_status = 'Delivered';
            break;
        case 'cancel_order':
            $new_status = 'Cancelled';
            break;
        case 'delete_order':
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
            $stmt->execute(['id' => $order_id]);
            header("Location: admin_manage_orders.php");
            exit;
        default:
            $new_status = null;
            break;
    }

    if ($new_status) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $new_status, 'id' => $order_id]);
        header("Location: view_order.php?id=" . $order_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        .sidebar {
            background-color: #343a40;
            color: #ffffff;
            min-height: 100vh;
            padding: 15px;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar h3 {
            font-size: 1.2rem;
            color: #ffc107;
        }
        .sidebar .nav-link {
            color: #ffffff;
            font-size: 0.9rem;
        }
        .sidebar .nav-link:hover {
            color: #ffc107;
        }
        .order-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
            font-size: 0.9rem;
            margin-left: 220px; /* Adjust for sidebar width */
        }
        body {
            background-color: #e9ecef;
        }
        .back-button {
            margin-bottom: 15px;
        }
        .btn {
            font-size: 0.85rem;
            padding: 4px 10px;
        }
        .table th, .table td {
            padding: 6px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Admin Panel</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="../pages/manage_orders.php">Manage Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../pages/manage_products.php">Manage Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../pages/manage_customers.php">Manage Customers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../pages/manage_categories.php">Manage Categories</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../pages/manage_locations.php">Manage Locations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../pages/reports.php">Reports</a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="container mt-4 order-container">
    <li class="nav-item back-button" style="list-style: none">
        <a class="nav-link" href="../pages/manage_orders.php">Back</a>
    </li>
    <h2>Order Details</h2>
    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']); ?></p>
    <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']); ?></p>
    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($order['location_name']); ?></p>
    <p><strong>Total Amount:</strong> <?= htmlspecialchars($order['total_amount']); ?></p>
    <p><strong>Delivery Fee:</strong> <?= htmlspecialchars($order['delivery_fee']); ?></p>

    <h4>Items Ordered</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td><?= htmlspecialchars($item['price']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h4>Order Actions</h4>
    <form method="POST" action="">
        <?php if ($order['status'] === 'Pending'): ?>
            <button type="submit" name="action" value="confirm_payment" class="btn btn-success">Confirm Payment</button>
            <button type="submit" name="action" value="process_order" class="btn btn-primary">Process Order</button>
            <button type="submit" name="action" value="cancel_order" class="btn btn-warning">Cancel Order</button>
            <button type="submit" name="action" value="delete_order" class="btn btn-danger">Delete Order</button>
        <?php elseif ($order['status'] === 'Processed'): ?>
            <button type="submit" name="action" value="deliver_order" class="btn btn-primary">Deliver Order</button>
            <button type="submit" name="action" value="cancel_order" class="btn btn-warning">Cancel Order</button>
        <?php elseif ($order['status'] === 'Delivered'): ?>
            <button type="submit" name="action" value="cancel_order" class="btn btn-warning">Cancel Order</button>
            <button type="submit" name="action" value="delete_order" class="btn btn-danger">Delete Order</button>
        <?php elseif ($order['status'] === 'Cancelled'): ?>
            <button type="submit" name="action" value="process_order" class="btn btn-primary">Process Order</button>
            <button type="submit" name="action" value="deliver_order" class="btn btn-primary">Deliver Order</button>
            <button type="submit" name="action" value="delete_order" class="btn btn-danger">Delete Order</button>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
