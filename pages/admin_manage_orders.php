<?php
include('../includes/header.php');
include('../config/database.php');


// Fetch locations for filter dropdown
$locations = $pdo->query("SELECT id, location_name FROM store_locations")->fetchAll(PDO::FETCH_ASSOC);

// Fetch and filter orders based on status, location, and date
function fetchOrders($pdo, $status, $location_id = null, $start_date = null, $end_date = null) {
    $query = "SELECT o.id, o.order_date, o.transaction_id, c.name AS customer_name, c.phone, o.total_amount
              FROM orders o
              JOIN customers c ON o.customer_id = c.id
              WHERE o.status = :status";
    
    $params = ['status' => $status];
    
    if ($location_id) {
        $query .= " AND o.location_id = :location_id";
        $params['location_id'] = $location_id;
    }
    
    if ($start_date && $end_date) {
        $query .= " AND o.order_date BETWEEN :start_date AND :end_date";
        $params['start_date'] = $start_date;
        $params['end_date'] = $end_date;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update order status based on admin action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'confirm_payment':
            $new_status = 'Processed';
            break;
        case 'process_order':
            $new_status = 'Processed';
            break;
        case 'cancel_order':
            $new_status = 'Cancelled';
            break;
        case 'deliver_order':
            $new_status = 'Delivered';
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
    }
}

// Retrieve order details
function getOrderDetails($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT o.order_date, o.transaction_id, c.name AS customer_name, c.phone, c.email, o.address, 
               o.total_amount, l.delivery_fee, p.name AS product_name, oi.quantity, oi.price
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        JOIN store_locations l ON o.location_id = l.id
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = :order_id
    ");
    $stmt->execute(['orders_id' => $order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Display orders by status
function displayOrders($pdo, $status, $location_id = null, $start_date = null, $end_date = null) {
    $orders = fetchOrders($pdo, $status, $location_id, $start_date, $end_date);
    
    echo "<h2>$status Orders</h2>";
    echo "<table border='1'>
            <tr>
                <th>Order Date</th>
                <th>Transaction ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Total Amount</th>
                <th>Details</th>
            </tr>";

    foreach ($orders as $order) {
        echo "<tr>
                <td>{$order['order_date']}</td>
                <td>{$order['transaction_id']}</td>
                <td>{$order['customer_name']}</td>
                <td>{$order['phone']}</td>
                <td>{$order['total_amount']}</td>
                <td><a href='admin_manage_orders.php?order_id={$order['id']}'>View Details</a></td>
              </tr>";
    }
    echo "</table>";
}

// Display order details with actions
if (isset($_GET['orders_id'])) {
    $order_id = $_GET['orders_id'];
    $orderDetails = getOrderDetails($pdo, $order_id);

    if ($orderDetails) {
        $order = $orderDetails[0];
        echo "<h3>Order Details</h3>";
        echo "<p>Order Date: {$order['order_date']}</p>";
        echo "<p>Transaction ID: {$order['transaction_id']}</p>";
        echo "<p>Customer Name: {$order['customer_name']}</p>";
        echo "<p>Phone: {$order['phone']}</p>";
        echo "<p>Email: {$order['email']}</p>";
        echo "<p>Delivery Address: {$order['address']}</p>";
        echo "<p>Total Amount: {$order['total_amount']}</p>";
        echo "<p>Delivery Fee: {$order['delivery_fee']}</p>";
        
        echo "<h4>Items Ordered</h4>";
        echo "<table border='1'>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>";
        foreach ($orderDetails as $item) {
            echo "<tr>
                    <td>{$item['product_name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>{$item['price']}</td>
                  </tr>";
        }
        echo "</table>";
        
        echo "<h4>Order Actions</h4>";
        echo "<form method='POST' action=''>
                <input type='hidden' name='order_id' value='$order_id'>
                <button type='submit' name='action' value='confirm_payment'>Confirm Payment</button>
                <button type='submit' name='action' value='process_order'>Process Order</button>
                <button type='submit' name='action' value='deliver_order'>Deliver Order</button>
                <button type='submit' name='action' value='cancel_order'>Cancel Order</button>
                <button type='submit' name='action' value='delete_order'>Delete Order</button>
              </form>";
    }
} else {
    // Filter form
    echo "<form method='GET'>
            <label>Location:</label>
            <select name='location_id'>
                <option value=''>All</option>";
    foreach ($locations as $location) {
        echo "<option value='{$location['id']}'>{$location['location_name']}</option>";
    }
    echo "</select>
          <label>Start Date:</label><input type='date' name='start_date'>
          <label>End Date:</label><input type='date' name='end_date'>
          <button type='submit'>Filter</button>
          </form>";

    // Display orders by status
    displayOrders($pdo, 'Pending', $_GET['location_id'] ?? null);
    displayOrders($pdo, 'Processed', $_GET['location_id'] ?? null);
    displayOrders($pdo, 'Delivered', $_GET['location_id'] ?? null, $_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
    displayOrders($pdo, 'Cancelled', $_GET['location_id'] ?? null, $_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
}
?>
