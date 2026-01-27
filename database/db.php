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