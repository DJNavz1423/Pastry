<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['employee_name']);
    $email = sanitize_input($_POST['employee_email']);
    $phone = sanitize_input($_POST['employee_phone']);
    $position = sanitize_input($_POST['employee_position']);
    $salary = isset($_POST['employee_salary']) ? floatval($_POST['employee_salary']) : 0;
    $hire_date = sanitize_input($_POST['employee_hire_date']);
    $status = sanitize_input($_POST['employee_status']);
    
    $sql = "INSERT INTO employees (full_name, email, phone, position, salary, hire_date, status) 
            VALUES ('$full_name', '$email', '$phone', '$position', $salary, '$hire_date', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Employee added successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>