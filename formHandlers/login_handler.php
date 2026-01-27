<?php
session_start();
require '../database/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
  $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';

  if(empty($email) || empty($password)){
    echo "ALL FIELDS ARE REQUIRED.";
    exit;
  }

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  
  if($stmt === false){
    die('Prepare failed: ' . htmlspecialchars($conn->error));
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['password'])){
      $_SESSION['email'] = $user['email'];
      header('Location: ../dashboard.php');
      exit;
    }

    else{
      echo "INVALID EMAIL OR PASSWORD.";
    }
  }

  else{
    echo "INVALID EMAIL OR PASSWORD.";
  }

  $stmt->close();
}