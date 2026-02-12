<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$sql = "SELECT o.*, u.full_name as customer_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.id DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>