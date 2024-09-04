<?php

session_start();

include_once('../config/connect_db.php');

$dept_name = $_POST['dept'];

$data_json = array();

// Check for duplicate type name
$duplicate_check_sql = "SELECT COUNT(*) FROM department WHERE dept_name = ?";
$duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
mysqli_stmt_bind_param($duplicate_stmt, "s", $dept_name);
mysqli_stmt_execute($duplicate_stmt);
mysqli_stmt_bind_result($duplicate_stmt, $count);
mysqli_stmt_fetch($duplicate_stmt);
mysqli_stmt_close($duplicate_stmt);

if ($count > 0) {
    $data_json = array("status" => "error", "message" => "There is already a department with this name.");
    echo json_encode($data_json);
    exit();
}

// Insert type into the database
$sql = "INSERT INTO department (dept_name) VALUES(?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $dept_name);

$result = mysqli_stmt_execute($stmt);
if ($result) {
    $data_json = array("status" => "successfully");
} else {
    $data_json = array("status" => "error", "message" => "Failed to add type.");
}

echo json_encode($data_json);

mysqli_stmt_close($stmt);
mysqli_close($conn);