<?php
include_once('../config/connect_db.php');

$data_json = array();
$cart_detail_id = $_POST['cart_detail_id'];

$sql = "DELETE FROM cart_detail WHERE cart_detail_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $cart_detail_id);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($result) {
    $data_json = array("status" => "Product has been removed from cart.", "color" => "success");
} else {
    $data_json = array("status" => "Delete Error", "color" => "error");
}

echo json_encode($data_json);
mysqli_close($conn);
