<?php

session_start();

include_once('../config/connect_db.php');

$id = $_POST['id'];
$name = $_POST['name'];
$stock = $_SESSION['user_stock'];

$data_json = array();

if (isset($_POST['id'])) {

    // Check for duplicate product name
    $duplicate_check_sql = "SELECT COUNT(*) FROM product_type WHERE prod_type_desc = ? AND st_id = ? AND prod_type_id != ?";
    $duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
    mysqli_stmt_bind_param($duplicate_stmt, "sii", $name, $stock, $id);
    mysqli_stmt_execute($duplicate_stmt);
    mysqli_stmt_bind_result($duplicate_stmt, $count);
    mysqli_stmt_fetch($duplicate_stmt);
    mysqli_stmt_close($duplicate_stmt);

    if ($count > 0) {
        $data_json = array("status" => "error", "message" => "There is already a type with this name.");
        echo json_encode($data_json);
        exit();
    }

    // Update product type
    $sql = "UPDATE product_type SET prod_type_desc = ?, st_id = ? WHERE prod_type_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $name, $stock, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $data_json = array("status" => "successfully");
    } else {
        $data_json = array("status" => "error", "message" => "Failed to update product type.");
    }

    mysqli_stmt_close($stmt);
} else {
    $data_json = array("status" => "error", "message" => "Product ID not found");
}

echo json_encode($data_json);

mysqli_close($conn);
