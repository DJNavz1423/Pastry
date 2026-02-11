<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pastry";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
  die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset("utf8mb4");

function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to check if admin is logged in
function is_admin_logged_in() {
    session_start();
    return isset($_SESSION['admin_id']);
}

// Function to redirect if not logged in
function require_admin_login() {
    if (!is_admin_logged_in()) {
        header("Location: admin_login.php");
        exit();
    }
}