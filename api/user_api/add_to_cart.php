<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = get_current_user_id();
    $product_id = $conn->real_escape_string($data['product_id']);
    $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
    
    if ($quantity < 1) {
        $response['message'] = 'Invalid quantity';
        echo json_encode($response);
        exit;
    }
    
    // Check if product exists and is not archived
    $product_check = $conn->query("SELECT id FROM products WHERE id = '$product_id' AND is_archived = 0");
    if ($product_check->num_rows === 0) {
        $response['message'] = 'Product not found';
        echo json_encode($response);
        exit;
    }
    
    // Check if already in cart
    $check = $conn->query("SELECT quantity FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'");
    
    if ($check->num_rows > 0) {
        // Update quantity
        $current = $check->fetch_assoc();
        $new_quantity = $current['quantity'] + $quantity;
        $sql = "UPDATE cart SET quantity = $new_quantity WHERE user_id = '$user_id' AND product_id = '$product_id'";
    } else {
        // Insert new
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', $quantity)";
    }
    
    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Added to cart';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>