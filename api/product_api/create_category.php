<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['category_name']);
    $description = sanitize_input($_POST['category_description']);
    
    // Check if category already exists
    $check = $conn->query("SELECT id FROM categories WHERE name = '$name'");
    if ($check->num_rows > 0) {
        $response['message'] = 'Category already exists!';
        echo json_encode($response);
        exit;
    }
    
    $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Category added successfully!';
        $response['category_name'] = $name;
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>