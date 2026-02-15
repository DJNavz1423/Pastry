<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$user_id = get_current_user_id();
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : null;

$sql = "SELECT p.*, f.added_at,
        EXISTS(SELECT 1 FROM cart c WHERE c.product_id = p.id AND c.user_id = '$user_id') as in_cart
        FROM favorites f
        JOIN products p ON f.product_id = p.id
        WHERE f.user_id = '$user_id' AND p.is_archived = 0";

if ($category && $category !== 'all') {
    $sql .= " AND p.category = '$category'";
}

$sql .= " ORDER BY f.added_at DESC";

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['in_cart'] = (bool)$row['in_cart'];
        $products[] = $row;
    }
}

echo json_encode($products);
?>