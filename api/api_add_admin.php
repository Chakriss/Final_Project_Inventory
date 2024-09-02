<?php

session_start();
include_once('../config/connect_db.php');

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = base64_encode(trim($_POST['password']));
$confirmpassword = base64_encode(trim($_POST['confirmpassword']));
$dept = trim($_POST['dept']);
$stock = trim($_POST['stock']);
$status = trim($_POST['status']);
$us_level = 'A';

$data_json = array();

// Check for duplicate user email
$duplicate_check_sql = "SELECT COUNT(*) FROM user WHERE us_email = ?";
$duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);

if ($duplicate_stmt) {
    mysqli_stmt_bind_param($duplicate_stmt, "s", $email);
    mysqli_stmt_execute($duplicate_stmt);
    mysqli_stmt_bind_result($duplicate_stmt, $count);
    mysqli_stmt_fetch($duplicate_stmt);
    mysqli_stmt_close($duplicate_stmt);

    if ($count > 0) {
        $data_json = array("status" => "error", "message" => "A user with this email already exists.");
        echo json_encode($data_json);
        exit();
    }
} else {
    $data_json = array("status" => "error", "message" => "Failed to check for duplicate email.");
    echo json_encode($data_json);
    exit();
}

// Check if passwords match
if ($password !== $confirmpassword) {
    $data_json = array("status" => "error", "message" => "Passwords don't match");
    echo json_encode($data_json);
    exit();
}

// Insert new user
$sql = "INSERT INTO user (us_name, us_email, us_password, us_level, dept_id, st_id, us_status_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssssiis", $name, $email, $password, $us_level, $dept, $stock, $status);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $data_json = array("status" => "successfully");
    } else {
        $data_json = array("status" => "error", "message" => "Failed to add user.");
    }

    mysqli_stmt_close($stmt);
} else {
    $data_json = array("status" => "error", "message" => "Failed to prepare statement.");
}

echo json_encode($data_json);
mysqli_close($conn);
