<?php
session_start();
require '../database/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $fullName = htmlspecialchars(trim($_POST['name']));
  $email = htmlspecialchars(trim($_POST['email']));
  $password = htmlspecialchars(trim($_POST['confirm_password']));

  if(empty($fullName) || empty($email) || empty($password)){
    echo "ALL FIELDS ARE REQUIRED.";
    exit;
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (fullName, email, password) VALUES (?, ?, ?)");

  if($stmt === false){
    die('Prepare Failed: ' . htmlspecialchars($conn->error));
  }

  $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
  
  if($stmt->execute()){
    $_SESSION['email'] = $email;
    header('Location: ../dashboard.php');
  }

  else{
    echo "Error: Could not register user.";
  }
$stmt->close();
}