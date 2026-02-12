<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id']);
    $status = $conn->real_escape_string($data['status']);
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $response['message'] = 'Invalid status value';
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE orders SET status = '$status' WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Order status updated successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>