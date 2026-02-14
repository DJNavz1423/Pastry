<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $username = sanitize_input($_POST['profile_username']);
    $full_name = sanitize_input($_POST['profile_fullname']);
    $email = sanitize_input($_POST['profile_email']);
    
    // Verify the admin is updating their own profile
    if ($admin_id != $_SESSION['admin_id']) {
        $response['message'] = 'Unauthorized: You can only edit your own profile.';
        echo json_encode($response);
        exit;
    }
    
    // Check if username already exists (excluding current admin)
    $check_username = $conn->query("SELECT id FROM admins WHERE username = '$username' AND id != $admin_id");
    if ($check_username->num_rows > 0) {
        $response['message'] = 'Username already exists. Please choose another.';
        echo json_encode($response);
        exit;
    }
    
    // Check if email already exists (excluding current admin)
    $check_email = $conn->query("SELECT id FROM admins WHERE email = '$email' AND id != $admin_id");
    if ($check_email->num_rows > 0) {
        $response['message'] = 'Email already exists. Please use another email.';
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE admins SET 
            username = '$username',
            full_name = '$full_name',
            email = '$email'
            WHERE id = $admin_id";
    
    if ($conn->query($sql) === TRUE) {
        // Update session if username changed
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_name'] = $full_name;
        
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>