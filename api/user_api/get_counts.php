<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$user_id = get_current_user_id();

// Get cart count
$cart_count = $conn->query("SELECT COUNT(*) as count FROM cart WHERE user_id = '$user_id'")->fetch_assoc()['count'];

// Get favorites count
$fav_count = $conn->query("SELECT COUNT(*) as count FROM favorites WHERE user_id = '$user_id'")->fetch_assoc()['count'];

echo json_encode([
    'cart_count' => $cart_count,
    'favorites_count' => $fav_count
]);
?>