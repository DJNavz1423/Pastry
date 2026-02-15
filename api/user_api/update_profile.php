<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = get_current_user_id();
    $full_name = $conn->real_escape_string($_POST['user_fullname']);
    $email = $conn->real_escape_string($_POST['user_email']);
    $phone = $conn->real_escape_string($_POST['user_phone']);
    $address = $conn->real_escape_string($_POST['user_address']);
    
    // Check if email already exists (excluding current user)
    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != '$user_id'");
    if ($check_email->num_rows > 0) {
        $response['message'] = 'Email already exists';
        echo json_encode($response);
        exit;
    }
    
    // Check if phone already exists (excluding current user)
    $check_phone = $conn->query("SELECT id FROM users WHERE phone = '$phone' AND id != '$user_id'");
    if ($check_phone->num_rows > 0) {
        $response['message'] = 'Phone number already exists';
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE users SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            address = '$address'
            WHERE id = '$user_id'";
    
    if ($conn->query($sql)) {
        // Update session
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>