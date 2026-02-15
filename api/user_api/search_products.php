<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$user_id = get_current_user_id();
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($search)) {
    echo json_encode([]);
    exit;
}

// Search in product name, description, and category
$sql = "SELECT p.*, 
        EXISTS(SELECT 1 FROM favorites f WHERE f.product_id = p.id AND f.user_id = '$user_id') as is_favorite,
        EXISTS(SELECT 1 FROM cart c WHERE c.product_id = p.id AND c.user_id = '$user_id') as in_cart
        FROM products p
        WHERE p.is_archived = 0
        AND (
            p.name LIKE '%$search%' 
            OR p.description LIKE '%$search%'
            OR p.category LIKE '%$search%'
        )
        ORDER BY p.name ASC
        LIMIT 50";

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['is_favorite'] = (bool)$row['is_favorite'];
        $row['in_cart'] = (bool)$row['in_cart'];
        $products[] = $row;
    }
}

echo json_encode($products);
?>