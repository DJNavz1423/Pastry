<?php
session_start();
require '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $fullName = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $password = trim($_POST['password']);
  $confirmPassword = trim($_POST['confirm_password']);

  if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
    echo "ALL FIELDS ARE REQUIRED.";
    exit;
  }

  if ($password !== $confirmPassword) {
    echo "Passwords do not match.";
    exit;
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)"
  );

  if (!$stmt) {
    die("Prepare Failed: " . $conn->error);
  }

  $stmt->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);

  if ($stmt->execute()) {
    $_SESSION['email'] = $email;
    header("Location: ../login_page.html");
    exit;
  } else {
    echo "Database Error: " . $stmt->error;
  }

  $stmt->close();
}