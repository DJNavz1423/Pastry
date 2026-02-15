<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$user_id = get_current_user_id();
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// Base query with LEFT JOINs to check favorites and cart
$sql = "SELECT p.*, 
        EXISTS(SELECT 1 FROM favorites f WHERE f.product_id = p.id AND f.user_id = '$user_id') as is_favorite,
        EXISTS(SELECT 1 FROM cart c WHERE c.product_id = p.id AND c.user_id = '$user_id') as in_cart
        FROM products p
        WHERE p.is_archived = 0";

// Filter by category
if ($category && $category !== 'all') {
    $sql .= " AND p.category = '$category'";
}

// Sorting
if ($sort === 'bestselling') {
    // For now, random order - you can implement actual bestseller logic
    $sql .= " ORDER BY RAND()";
} else {
    $sql .= " ORDER BY p.created_at DESC";
}

// Limit
if ($limit) {
    $sql .= " LIMIT $limit";
}

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convert boolean fields
        $row['is_favorite'] = (bool)$row['is_favorite'];
        $row['in_cart'] = (bool)$row['in_cart'];
        $products[] = $row;
    }
}

echo json_encode($products);
?>