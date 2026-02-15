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
    $quantity = intval($data['quantity']);
    
    if ($quantity < 1) {
        $response['message'] = 'Invalid quantity';
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE cart SET quantity = $quantity WHERE user_id = '$user_id' AND product_id = '$product_id'";
    
    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Cart updated';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>