<?php
session_start();
include_once('../config/connect_db.php');

$data_json = array();

if (isset($_SESSION['user_id'])) {
    $status = 'P';  
    
    $all_sql = "SELECT COUNT(*) FROM cart WHERE cart_status_id = ?";
    $all_stmt = mysqli_prepare($conn, $all_sql);
    mysqli_stmt_bind_param($all_stmt, "s", $status);
    mysqli_stmt_execute($all_stmt);
    mysqli_stmt_bind_result($all_stmt, $cart_all);
    mysqli_stmt_fetch($all_stmt);
    mysqli_stmt_close($all_stmt);

    // If there is no cart, return zero items
    if (!$cart_all) {
        $data_json = array("status" => "success", "total_items" => 0);
    } else {
        $data_json = array("status" => "success", "total_items" => $cart_all);
    }
} else {
    $data_json = array("status" => "error", "message" => "User not logged in");
}

echo json_encode($data_json);
mysqli_close($conn);
?>