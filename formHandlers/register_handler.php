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

  // Generate custom ID for user (U + 4 digits)
  $userId = generate_custom_id('U', 'users');

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare(
    "INSERT INTO users (id, full_name, email, phone, password) VALUES (?, ?, ?, ?, ?)"
  );

  if (!$stmt) {
    die("Prepare Failed: " . $conn->error);
  }

  $stmt->bind_param("sssss", $userId, $fullName, $email, $phone, $hashedPassword);

  if ($stmt->execute()) {
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $userId;
    header("Location: ../login_page.html");
    exit;
  } else {
    echo "Database Error: " . $stmt->error;
  }

  $stmt->close();
}
?>