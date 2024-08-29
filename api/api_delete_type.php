<?php
include_once('../config/connect_db.php');

$prod_type_id = $_POST['prod_type_id'];

$sql_check = "SELECT COUNT(*) FROM product WHERE prod_type_id = ?";
$check_stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($check_stmt, "i", $prod_type_id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_bind_result($check_stmt, $count);
mysqli_stmt_fetch($check_stmt);
mysqli_stmt_close($check_stmt);

if ($count > 0) {
    $data_json = array("status" => "error", "message" => "There are products that use this type of product.");
    echo json_encode($data_json);
    exit();
}

$sql = "DELETE FROM product_type WHERE prod_type_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $prod_type_id);
$result = mysqli_stmt_execute($stmt);


if ($result) {
    $data_json = array("status" => "The type has been successfully deleted.", "color" => "success");
} else {
    $data_json = array("status" => "Delete Error", "color" => "error");
}


echo json_encode($data_json);
mysqli_close($conn);
