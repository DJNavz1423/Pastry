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
    
    // Check if favorite exists
    $check = $conn->query("SELECT id FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");
    
    if ($check->num_rows > 0) {
        // Remove from favorites
        $sql = "DELETE FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'";
        if ($conn->query($sql)) {
            $response['success'] = true;
            $response['action'] = 'removed';
            $response['message'] = 'Removed from favorites';
        }
    } else {
        // Add to favorites
        $sql = "INSERT INTO favorites (user_id, product_id) VALUES ('$user_id', '$product_id')";
        if ($conn->query($sql)) {
            $response['success'] = true;
            $response['action'] = 'added';
            $response['message'] = 'Added to favorites';
        }
    }
}

echo json_encode($response);
?>