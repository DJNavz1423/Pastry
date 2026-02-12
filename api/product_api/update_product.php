<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['product_id']);
    $name = sanitize_input($_POST['product_name']);
    $description = sanitize_input($_POST['product_description']);
    $category = sanitize_input($_POST['product_category']);
    $price = floatval($_POST['product_price']);
    $quantity = intval($_POST['product_quantity']);
    
    // Get current product data
    $current_product = $conn->query("SELECT picture FROM products WHERE id = $id")->fetch_assoc();
    $picture = $current_product['picture'];
    
    // Handle file upload
    if (isset($_FILES['product_picture']) && $_FILES['product_picture']['error'] === 0) {
        $upload_dir = '../../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Delete old picture if exists
        if ($picture && file_exists($upload_dir . $picture)) {
            unlink($upload_dir . $picture);
        }
        
        $file_extension = pathinfo($_FILES['product_picture']['name'], PATHINFO_EXTENSION);
        $picture = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $picture;
        
        move_uploaded_file($_FILES['product_picture']['tmp_name'], $target_file);
    }
    
    $sql = "UPDATE products SET 
            name = '$name',
            description = '$description',
            category = '$category',
            price = $price,
            quantity = $quantity,
            picture = '$picture'
            WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Product updated successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>