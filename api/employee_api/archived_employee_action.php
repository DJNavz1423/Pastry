<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = intval($data['id']);
    $action = isset($data['action']) ? $data['action'] : '';

    if ($action === 'restore') {
        $sql = "UPDATE employees SET is_archived = 0, archived_at = NULL WHERE id = $id AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Employee restored successfully.';
        } else {
            $response['message'] = 'Employee not found in archive.';
        }

    } elseif ($action === 'delete_permanent') {
        $sql = "DELETE FROM employees WHERE id = $id AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Employee permanently deleted.';
        } else {
            $response['message'] = 'Employee not found in archive.';
        }

    } else {
        $response['message'] = 'Invalid action.';
    }
}

echo json_encode($response);
?>