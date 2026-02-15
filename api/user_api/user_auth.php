<?php
// User authentication helper
function require_user_login() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Please log in']);
        exit();
    }
}

function get_current_user_id() {
    return $_SESSION['user_id'];
}
?>