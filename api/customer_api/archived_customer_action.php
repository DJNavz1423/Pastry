<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = sanitize_input($data['id']);
    $action = isset($data['action']) ? $data['action'] : '';

    if ($action === 'restore') {
        $sql = "UPDATE users SET is_archived = 0, archived_at = NULL WHERE id = '$id' AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Customer restored successfully.';
        } else {
            $response['message'] = 'Customer not found in archive.';
        }

    } elseif ($action === 'delete_permanent') {
        $sql = "DELETE FROM users WHERE id = '$id' AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Customer permanently deleted.';
        } else {
            $response['message'] = 'Customer not found in archive.';
        }

    } else {
        $response['message'] = 'Invalid action.';
    }
}

echo json_encode($response);
?>