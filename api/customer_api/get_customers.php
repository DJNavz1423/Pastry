<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

// Add created_at column if it doesn't exist in users table
$sql = "SELECT id, full_name, email, phone, 
        created_at 
        FROM users 
        WHERE is_archived = 0
        ORDER BY id DESC";

$result = $conn->query($sql);

$customers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

echo json_encode($customers);
?>