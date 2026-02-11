<?php
require_once '../Pastry/database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id     = intval($data['id']);
    $action = isset($data['action']) ? $data['action'] : '';

    if ($action === 'restore') {
        $sql = "UPDATE products SET is_archived = 0, archived_at = NULL WHERE id = $id AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Product restored successfully.';
        } else {
            $response['message'] = 'Product not found in archive.';
        }

    } elseif ($action === 'delete_permanent') {
        // Fetch picture filename before deleting
        $row = $conn->query("SELECT picture FROM products WHERE id = $id AND is_archived = 1")->fetch_assoc();

        $sql = "DELETE FROM products WHERE id = $id AND is_archived = 1";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            // Remove image file permanently
            if ($row && $row['picture']) {
                $file_path = '../uploads/products/' . $row['picture'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $response['success'] = true;
            $response['message'] = 'Product permanently deleted.';
        } else {
            $response['message'] = 'Product not found in archive.';
        }

    } else {
        $response['message'] = 'Invalid action.';
    }
}

echo json_encode($response);
?>