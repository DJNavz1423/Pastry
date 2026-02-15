<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = get_current_user_id();
    
    $current_password = $data['current_password'];
    $new_password = $data['new_password'];
    $confirm_password = $data['confirm_password'];
    
    // Validate
    if ($new_password !== $confirm_password) {
        $response['message'] = 'New passwords do not match';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($new_password) < 6) {
        $response['message'] = 'Password must be at least 6 characters';
        echo json_encode($response);
        exit;
    }
    
    // Get current password
    $user_query = $conn->query("SELECT password FROM users WHERE id = '$user_id'");
    if ($user_query->num_rows === 0) {
        $response['message'] = 'User not found';
        echo json_encode($response);
        exit;
    }
    
    $user = $user_query->fetch_assoc();
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $response['message'] = 'Current password is incorrect';
        echo json_encode($response);
        exit;
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update
    $sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
    
    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Password changed successfully';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>