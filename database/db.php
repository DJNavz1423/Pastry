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

/**
 * Generate a unique custom ID
 * Format: {PREFIX}{4-DIGIT-NUMBER}
 * Example: P1234, E5678, O9012, U4567
 * 
 * @param string $prefix - Single letter prefix (P, E, O, U)
 * @param string $table - Table name to check for uniqueness
 * @return string - Generated unique ID
 */
function generate_custom_id($prefix, $table) {
    global $conn;
    
    $prefix = strtoupper($prefix); // Ensure prefix is uppercase
    $max_attempts = 100; // Prevent infinite loop
    $attempt = 0;
    
    do {
        // Generate 4-digit random number (1000-9999)
        $random_number = rand(1000, 9999);
        $custom_id = $prefix . $random_number;
        
        // Check if ID already exists in the table
        $check_sql = "SELECT id FROM $table WHERE id = '$custom_id'";
        $result = $conn->query($check_sql);
        
        $attempt++;
        
        // If ID doesn't exist, we can use it
        if ($result->num_rows === 0) {
            return $custom_id;
        }
        
    } while ($attempt < $max_attempts);
    
    // Fallback: if we couldn't generate unique ID after max attempts
    // Add timestamp to ensure uniqueness
    return $prefix . rand(1000, 9999) . substr(time(), -2);
}