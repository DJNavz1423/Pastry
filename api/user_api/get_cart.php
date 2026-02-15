<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$user_id = get_current_user_id();
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

$sql = "SELECT c.*, p.name, p.price, p.picture, p.category
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = '$user_id' AND p.is_archived = 0
        ORDER BY c.added_at DESC";

if ($limit) {
    $sql .= " LIMIT $limit";
}

$result = $conn->query($sql);

$items = [];
$total = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $item_total = $row['price'] * $row['quantity'];
        $total += $item_total;
        $items[] = $row;
    }
}

echo json_encode([
    'items' => $items,
    'total' => $total
]);
?>