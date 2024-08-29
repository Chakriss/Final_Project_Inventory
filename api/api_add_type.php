<?php

session_start();

include_once('../config/connect_db.php');
$stock = $_SESSION["user_stock"];
$type_name = $_POST['type_name'];

$data_json = array();

// Check for duplicate type name
$duplicate_check_sql = "SELECT COUNT(*) FROM product_type WHERE prod_type_desc = ? AND st_id = ?";
$duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
mysqli_stmt_bind_param($duplicate_stmt, "si", $type_name, $stock);
mysqli_stmt_execute($duplicate_stmt);
mysqli_stmt_bind_result($duplicate_stmt, $count);
mysqli_stmt_fetch($duplicate_stmt);
mysqli_stmt_close($duplicate_stmt);

if ($count > 0) {
    $data_json = array("status" => "error", "message" => "There is already a type with this name.");
    echo json_encode($data_json);
    exit();
}

// Insert type into the database
$sql = "INSERT INTO product_type (prod_type_desc, st_id) VALUES(?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $type_name, $stock);

$result = mysqli_stmt_execute($stmt);
if ($result) {
    $data_json = array("status" => "successfully");
} else {
    $data_json = array("status" => "error", "message" => "Failed to add type.");
}

echo json_encode($data_json);

mysqli_stmt_close($stmt);
mysqli_close($conn);