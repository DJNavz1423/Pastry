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
    $make_admin = isset($_POST['employee_make_admin']) && $_POST['employee_make_admin'] === 'true';
    
    $sql = "INSERT INTO employees (id, full_name, email, phone, position, salary, hire_date, status) 
            VALUES ('$id', '$full_name', '$email', '$phone', '$position', $salary, '$hire_date', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        // If make_admin is checked, create sub-admin account
        if ($make_admin) {
            // Generate username from email
            $username = explode('@', $email)[0];
            // Default password (they should change it)
            $default_password = 'password123';
            $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
            
            $admin_sql = "INSERT INTO admins (username, email, password, full_name, role) 
                         VALUES ('$username', '$email', '$hashed_password', '$full_name', 'sub_admin')";
            
            if ($conn->query($admin_sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Employee added successfully with ID: ' . $id . ' and created as Sub Admin. Default password: password123';
                $response['id'] = $id;
            } else {
                $response['success'] = true;
                $response['message'] = 'Employee added with ID: ' . $id . ' but failed to create admin account: ' . $conn->error;
                $response['id'] = $id;
            }
        } else {
            $response['success'] = true;
            $response['message'] = 'Employee added successfully with ID: ' . $id;
            $response['id'] = $id;
        }
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}

echo json_encode($response);
?>