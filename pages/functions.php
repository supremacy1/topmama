<?php
function getOrderStats($pdo, $customerId) {
    $statuses = ['Delivered', 'Pending', 'Processed', 'Cancelled'];
    $stats = [];

    foreach ($statuses as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = :customer_id AND status = :status");
        $stmt->execute(['customer_id' => $customerId, 'status' => $status]);
        $stats[$status] = $stmt->fetchColumn();
    }

    return $stats;
}
