<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = sanitize_input($_GET['id']);
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Customer not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>