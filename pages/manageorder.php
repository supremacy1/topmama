<?php
include('../includes/header.php');
include('../config/database.php');

// Function to count orders based on status
function countOrdersByStatus($pdo, $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = :status");
    $stmt->execute(['status' => $status]);
    return $stmt->fetchColumn();
}

// Function to count customers by status
function countCustomersByStatus($pdo, $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE status = :status");
    $stmt->execute(['status' => $status]);
    return $stmt->fetchColumn();
}

// Orders statistics
$pendingOrders = countOrdersByStatus($pdo, 'Pending');
$processedOrders = countOrdersByStatus($pdo, 'Processed');
$deliveredOrders = countOrdersByStatus($pdo, 'Delivered');
$canceledOrders = countOrdersByStatus($pdo, 'Cancelled');

// Customer statistics
$allCustomers = countCustomersByStatus($pdo, 'Active') + countCustomersByStatus($pdo, 'Inactive');
$activeCustomers = countCustomersByStatus($pdo, 'Active');
$inactiveCustomers = countCustomersByStatus($pdo, 'Inactive');

// Retrieve all customers with their order statistics
$customersStmt = $pdo->query("
    SELECT 
        customers.id AS customer_id,
        customers.name,
        customers.email,
        customers.phone,
        customers.address,
        customers.status,
        COUNT(orders.id) AS total_orders,
        SUM(CASE WHEN orders.status = 'Completed' THEN 1 ELSE 0 END) AS completed_orders,
        SUM(CASE WHEN orders.status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
    FROM customers
    LEFT JOIN orders ON customers.id = orders.customer_id
    GROUP BY customers.id
");

// Display orders and customer stats
echo "<h1>Manage Orders</h1>";
echo "<p>Pending Orders: $pendingOrders</p>";
echo "<p>Processed Orders: $processedOrders</p>";
echo "<p>Delivered Orders: $deliveredOrders</p>";
echo "<p>Cancelled Orders: $canceledOrders</p>";

echo "<h1>Manage Customers</h1>";
echo "<p>All Customers: $allCustomers</p>";
echo "<p>Active Customers: $activeCustomers</p>";
echo "<p>Inactive Customers: $inactiveCustomers</p>";

// Display customer details and order stats
echo "<h2>Customer Details</h2>";
echo "<table border='1'>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Status</th>
            <th>Total Orders</th>
            <th>Completed Orders</th>
            <th>Cancelled Orders</th>
        </tr>";
while ($row = $customersStmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['address']}</td>
            <td>{$row['status']}</td>
            <td>{$row['total_orders']}</td>
            <td>{$row['completed_orders']}</td>
            <td>{$row['cancelled_orders']}</td>
        </tr>";
}
echo "</table>";

// Reports section - filter transactions by date range
echo "<h1>Reports</h1>";
echo "<form method='GET' action=''>
        <label>Start Date:</label>
        <input type='date' name='start_date'>
        <label>End Date:</label>
        <input type='date' name='end_date'>
        <button type='submit'>Filter</button>
      </form>";

// Retrieve and filter transactions by date range
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE order_date BETWEEN :start_date AND :end_date");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);

    echo "<h2>Transaction Reports</h2>";
    echo "<table border='1'>
            <tr>
                <th>Transaction ID</th>
                <th>Customer ID</th>
                <th>Location ID</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>";
    while ($transaction = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$transaction['transaction_id']}</td>
                <td>{$transaction['customer_id']}</td>
                <td>{$transaction['location_id']}</td>
                <td>{$transaction['total_amount']}</td>
                <td>{$transaction['status']}</td>
                <td>{$transaction['order_date']}</td>
              </tr>";
    }
    echo "</table>";
}
?>
