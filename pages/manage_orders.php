<?php
// include('../includes/header.php');

include('../config/database.php');

// Fetch orders based on selected filter
$status = $_GET['status'] ?? 'All';
$query = "SELECT o.*, c.name as customer_name, s.location_name 
          FROM orders o 
          JOIN customers c ON o.customer_id = c.id
          JOIN store_locations s ON o.location_id = s.id";
if ($status !== 'All') {
    $query .= " WHERE o.status = :status";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['status' => $status]);
} else {
    $stmt = $pdo->query($query);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get counts of orders by status
$statuses = ['Pending', 'Processed', 'Delivered', 'Cancelled'];
$order_counts = [];

foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = :status");
    $stmt->execute(['status' => $status]);
    $order_counts[$status] = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .sidebar {
            height: 100vh; /* Full height */
            width: 150px;
            position: fixed; /* Fixed position */
            top: 7%;
            left: 0;
            overflow-y: auto;
        }
        .content {
            margin-left: 150px;
            margin-top:10% /* Adjust based on sidebar width */
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <!-- Main Content -->
    <div class="content p-4 flex-grow-1">
        <h2 class="mb-4">Order Management</h2>

        <!-- Order Counts Row with Colors -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <p class="card-text"><?= $order_counts['Pending'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Processed Orders</h5>
                        <p class="card-text"><?= $order_counts['Processed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Delivered Orders</h5>
                        <p class="card-text"><?= $order_counts['Delivered'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Cancelled Orders</h5>
                        <p class="card-text"><?= $order_counts['Cancelled'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter by Status -->
        <form method="GET" class="mb-3">
            <select name="status" onchange="this.form.submit()" class="form-control w-25">
                <option value="All" <?= ($status === 'All') ? 'selected' : ''; ?>>All</option>
                <option value="Pending" <?= ($status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Processed" <?= ($status === 'Processed') ? 'selected' : ''; ?>>Processed</option>
                <option value="Delivered" <?= ($status === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?= ($status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </form>

        <!-- Order List with Table Styling -->
        <table class="table table-hover table-bordered">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Order Date</th>
                    <th>Transaction ID</th>
                    <th>Customer</th>
                    <th>Location</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="
                        <?php 
                            echo ($order['status'] === 'Pending') ? 'table-warning' : 
                                 (($order['status'] === 'Processed') ? 'table-info' : 
                                 (($order['status'] === 'Delivered') ? 'table-success' : 
                                 'table-danger'));
                        ?>">
                        <td><?= htmlspecialchars($order['order_date']); ?></td>
                        <td><?= htmlspecialchars($order['transaction_id']); ?></td>
                        <td><?= htmlspecialchars($order['customer_name']); ?></td>
                        <td><?= htmlspecialchars($order['location_name']); ?></td>
                        <td><?= htmlspecialchars($order['total_amount']); ?></td>
                        <td><?= htmlspecialchars($order['status']); ?></td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id']; ?>" class="btn btn-sm btn-outline-info">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
