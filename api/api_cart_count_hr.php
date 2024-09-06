<?php
session_start();
include_once('../config/connect_db.php');

$data_json = array();

if (isset($_SESSION['user_id'])) {
    $us_id = $_SESSION['user_id'];
    $stock = 2;  // Assuming stock ID is fixed for the query
    $status = 'TBC';  // Assuming 'WC' is the status code for active carts

    // Step 1: Get the maximum cart_id
    $max_cart_sql = "SELECT MAX(cart_id) as max_cart_id FROM cart 
                     WHERE st_id = ? AND us_id = ? AND cart_status_id = ?";
    $max_cart_stmt = mysqli_prepare($conn, $max_cart_sql);
    mysqli_stmt_bind_param($max_cart_stmt, "iis", $stock, $us_id, $status);
    mysqli_stmt_execute($max_cart_stmt);
    mysqli_stmt_bind_result($max_cart_stmt, $max_cart_id);
    mysqli_stmt_fetch($max_cart_stmt);
    mysqli_stmt_close($max_cart_stmt);

    // If there is no cart, return zero items
    if (!$max_cart_id) {
        $data_json = array("status" => "success", "total_items" => 0);
    } else {
        // Step 2: Count items in the maximum cart_id
        $count_items_sql = "SELECT COUNT(cart_detail_id) as total_items FROM cart_detail 
                            WHERE cart_id = ?";
        $count_items_stmt = mysqli_prepare($conn, $count_items_sql);
        mysqli_stmt_bind_param($count_items_stmt, "i", $max_cart_id);
        mysqli_stmt_execute($count_items_stmt);
        mysqli_stmt_bind_result($count_items_stmt, $total_items);
        mysqli_stmt_fetch($count_items_stmt);
        mysqli_stmt_close($count_items_stmt);

        $data_json = array("status" => "success", "total_items" => $total_items);
    }
} else {
    $data_json = array("status" => "error", "message" => "User not logged in");
}

echo json_encode($data_json);
mysqli_close($conn);
?>
    
