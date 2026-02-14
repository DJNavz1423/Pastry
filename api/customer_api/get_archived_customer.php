<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$sql = "SELECT * FROM users WHERE is_archived = 1 ORDER BY archived_at DESC";
$result = $conn->query($sql);

$customers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

echo json_encode($customers);
?>