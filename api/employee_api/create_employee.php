<?php
require_once '../../database/db.php';
require_admin_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate custom ID for employee (E + 4 digits)
    $id = generate_custom_id('E', 'employees');
    
    $full_name = sanitize_input($_POST['employee_name']);
    $email = sanitize_input($_POST['employee_email']);
    $phone = sanitize_input($_POST['employee_phone']);
    $position = sanitize_input($_POST['employee_position']);
    $salary = isset($_POST['employee_salary']) ? floatval($_POST['employee_salary']) : 0;
    $hire_date = sanitize_input($_POST['employee_hire_date']);
    $status = sanitize_input($_POST['employee_status']);
    
    $sql = "INSERT INTO employees (id, full_name, email, phone, position, salary, hire_date, status) 
            VALUES ('$id', '$full_name', '$email', '$phone', '$position', $salary, '$hire_date', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Employee added successfully with ID: ' . $id;
        $response['id'] = $id;
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>