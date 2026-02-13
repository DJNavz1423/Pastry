<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = sanitize_input($data['id']); // Now a string like "P1234"

    // Soft-delete: mark as archived, do NOT delete the row or image
    $sql = "UPDATE products SET is_archived = 1, archived_at = NOW() WHERE id = '$id' AND is_archived = 0";

    if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Product moved to archive.';
    } else {
        $response['message'] = 'Product not found or already archived.';
    }
}

echo json_encode($response);
?>