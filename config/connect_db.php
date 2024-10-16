<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_stock";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for supporting Thai characters
mysqli_set_charset($conn, "utf8mb4");
