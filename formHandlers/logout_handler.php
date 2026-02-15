<?php
session_start();

$is_user = isset($_SESSION['user_id']) || isset($_SESSION['email']);
$is_admin = isset($_SESSION['admin_id']);

session_destroy();

if ($is_admin) {
    header('Location: ../admin_login.php');
} else {
    header('Location: ../login_page.html');
}
exit;
?>