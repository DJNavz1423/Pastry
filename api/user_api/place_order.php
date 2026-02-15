<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = get_current_user_id();
    $address = $conn->real_escape_string($data['address']);
    $phone = $conn->real_escape_string($data['phone']);
    $payment_method = $conn->real_escape_string($data['payment_method']);
    
    if (empty($address) || empty($phone)) {
        $response['message'] = 'Address and phone are required';
        echo json_encode($response);
        exit;
    }
    
    // Get cart items
    $cart_query = "SELECT c.product_id, c.quantity, p.price 
                   FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.user_id = '$user_id' AND p.is_archived = 0";
    $cart_result = $conn->query($cart_query);
    
    if ($cart_result->num_rows === 0) {
        $response['message'] = 'Cart is empty';
        echo json_encode($response);
        exit;
    }
    
    // Calculate total
    $cart_items = [];
    $total_amount = 0;
    while($item = $cart_result->fetch_assoc()) {
        $cart_items[] = $item;
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Add delivery fee
    $total_amount += 50;
    
    // Generate custom order ID (O + 4 digits)
    $order_id = generate_custom_id('O', 'orders');
    
    // Create order
    $sql = "INSERT INTO orders (id, user_id, total_amount, status, payment_method, shipping_address) 
            VALUES ('$order_id', '$user_id', $total_amount, 'pending', '$payment_method', '$address')";
    
    if ($conn->query($sql)) {
        // Insert order items
        foreach($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) 
                         VALUES ('$order_id', '$product_id', $quantity, $price)");
        }
        
        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id = '$user_id'");
        
        // Update user address if provided
        if (!empty($address)) {
            $conn->query("UPDATE users SET address = '$address' WHERE id = '$user_id'");
        }
        
        $response['success'] = true;
        $response['message'] = 'Order placed successfully';
        $response['order_id'] = $order_id;
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>