<?php
require_once '../../database/db.php';
require_once 'user_auth.php';
require_user_login();

header('Content-Type: application/json');

$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode($categories);
?>