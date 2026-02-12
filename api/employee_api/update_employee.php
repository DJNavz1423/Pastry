<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['employee_id']);
    $full_name = sanitize_input($_POST['employee_name']);
    $email = sanitize_input($_POST['employee_email']);
    $phone = sanitize_input($_POST['employee_phone']);
    $position = sanitize_input($_POST['employee_position']);
    $salary = isset($_POST['employee_salary']) ? floatval($_POST['employee_salary']) : 0;
    $hire_date = sanitize_input($_POST['employee_hire_date']);
    $status = sanitize_input($_POST['employee_status']);
    
    $sql = "UPDATE employees SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            position = '$position',
            salary = $salary,
            hire_date = '$hire_date',
            status = '$status'
            WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Employee updated successfully!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>