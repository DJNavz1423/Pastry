<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $current_password = $data['current_password'];
    $new_password = $data['new_password'];
    $confirm_new_password = $data['confirm_new_password'];
    
    // Validate new password
    if (strlen($new_password) < 6) {
        $response['message'] = 'New password must be at least 6 characters long.';
        echo json_encode($response);
        exit;
    }
    
    // Check if new passwords match
    if ($new_password !== $confirm_new_password) {
        $response['message'] = 'New passwords do not match.';
        echo json_encode($response);
        exit;
    }
    
    // Get current admin data
    $admin_id = $_SESSION['admin_id'];
    $admin_query = "SELECT password FROM admins WHERE id = $admin_id";
    $result = $conn->query($admin_query);
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Admin not found.';
        echo json_encode($response);
        exit;
    }
    
    $admin = $result->fetch_assoc();
    
    // Verify current password
    if (!password_verify($current_password, $admin['password'])) {
        $response['message'] = 'Current password is incorrect.';
        echo json_encode($response);
        exit;
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $sql = "UPDATE admins SET password = '$hashed_password' WHERE id = $admin_id";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Password changed successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>