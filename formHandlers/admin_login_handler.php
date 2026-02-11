<?php
require_once '../database/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            
            header("Location: ../admin.php");
            exit();
        } else {
            header("Location: ../admin_login.php?error=Invalid username or password");
            exit();
        }
    } else {
        header("Location: ../admin_login.php?error=Invalid username or password");
        exit();
    }
} else {
    header("Location: ../admin_login.php");
    exit();
}
?>