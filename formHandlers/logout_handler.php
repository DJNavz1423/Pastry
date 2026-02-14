<?php
session_start();

$is_admin = isset($_SESSION['admin_id']);
$is_user = isset($_SESSION['user_id']) || isset($_SESSION['email']);

session_destroy();

if ($is_admin) {
    header('Location: ../admin_login.php');
} else {
    header('Location: ../login_page.html');
}
exit;
?>