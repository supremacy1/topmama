<?php
// include('../includes/header.php');
include('../config/database.php');
include('./functions.php'); // Include functions.php to use getOrderStats

// Fetch all customers and count the total
$customer_counts = [
    'All' => 0
];

// Query to get all customer information
$query = "SELECT id, name, email FROM customers";
$stmt = $pdo->query($query);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count all customers
$customer_counts['All'] = count($customers);

$statuses = ['Pending', 'Processed', 'Delivered', 'Cancelled'];
$order_counts = [];

foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = :status");
    $stmt->execute(['status' => $status]);
    $order_counts[$status] = $stmt->fetchColumn();
}
?>
<div class="container mt-5">
<!--  -->
    <!-- Total Customer Count -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">All Customers</h5>
                    <p class="card-text"><?= $customer_counts['All'] ?? 0; ?></p>
                </div>
            </div>
        </div>
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
    </div>
    
    <!-- Customer List -->
    <h4>Customer Details</h4>
    <table class="table table-hover table-bordered">
        <thead class="bg-primary text-white">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= htmlspecialchars($customer['name']); ?></td>
                    <td><?= htmlspecialchars($customer['email']); ?></td>
                    <td>
                        <a href="view_customer.php?id=<?= $customer['id']; ?>" class="btn btn-sm btn-outline-info">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
