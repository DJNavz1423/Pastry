<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = sanitize_input($data['id']);

    // Soft-delete: mark as archived
    $sql = "UPDATE users SET is_archived = 1, archived_at = NOW() WHERE id = '$id' AND is_archived = 0";

    if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Customer moved to archive.';
    } else {
        $response['message'] = 'Customer not found or already archived.';
    }
}

echo json_encode($response);
?>