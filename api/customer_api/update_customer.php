<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = sanitize_input($_POST['customer_id']);
    $full_name = sanitize_input($_POST['customer_name']);
    $email = sanitize_input($_POST['customer_email']);
    $phone = sanitize_input($_POST['customer_phone']);
    
    $sql = "UPDATE users SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone'
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Customer updated successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>