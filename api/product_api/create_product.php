<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate custom ID for product (P + 4 digits)
    $id = generate_custom_id('P', 'products');
    
    $name = sanitize_input($_POST['product_name']);
    $description = sanitize_input($_POST['product_description']);
    $category = sanitize_input($_POST['product_category']);
    $price = floatval($_POST['product_price']);
    $quantity = intval($_POST['product_quantity']);
    
    // Handle file upload
    $picture = '';
    if (isset($_FILES['product_picture']) && $_FILES['product_picture']['error'] === 0) {
        $upload_dir = '../../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['product_picture']['name'], PATHINFO_EXTENSION);
        $picture = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $picture;
        
        if (move_uploaded_file($_FILES['product_picture']['tmp_name'], $target_file)) {
            // File uploaded successfully
        } else {
            $picture = '';
        }
    }
    
    $sql = "INSERT INTO products (id, name, description, category, price, quantity, picture) 
            VALUES ('$id', '$name', '$description', '$category', $price, $quantity, '$picture')";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Product added successfully with ID: ' . $id;
        $response['id'] = $id;
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>