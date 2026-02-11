<?php
require_once '../Pastry/database/db.php';
require_admin_login();

header('Content-Type: application/json');

$sql = "SELECT * FROM employees WHERE is_archived = 0 ORDER BY id DESC";
$result = $conn->query($sql);

$employees = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

echo json_encode($employees);
?>