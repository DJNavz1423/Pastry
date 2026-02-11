<?php
require_once '../Pastry/database/db.php';
require_admin_login();

header('Content-Type: application/json');

$sql = "SELECT * FROM products WHERE is_archived = 0 ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
?>