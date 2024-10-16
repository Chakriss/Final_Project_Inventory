<?php
session_start();
include_once('../config/connect_db.php');

$data_json = array();

if (isset($_POST['id'])) {
    $prod_id = $_POST['id'];
    $amount = $_POST['amount'];
    $detail = $_POST['detail'];
    $us_id = $_SESSION["user_id"];
    $stock = '1';  // Assuming stock ID is fixed for the query
    $status = 'TBC';  // Assuming 'WC' is the status code for active carts
    $dept_id = '0';
    $date = null;
    $time = null;

    // Check available stock for the product
    $stock_sql = "SELECT prod_amount FROM product WHERE prod_id = ?";
    $stock_stmt = mysqli_prepare($conn, $stock_sql);
    mysqli_stmt_bind_param($stock_stmt, "i", $prod_id);
    mysqli_stmt_execute($stock_stmt);
    mysqli_stmt_bind_result($stock_stmt, $available_stock);
    mysqli_stmt_fetch($stock_stmt);
    mysqli_stmt_close($stock_stmt);

    if ($amount > $available_stock) {
        $data_json = array("status" => "error", "message" => "Not enough stock available.");
        echo json_encode($data_json);
        exit();
    }

    // Check if there is an existing cart for the user
    $check_sql = "SELECT max(cart_id) as cart_id FROM cart WHERE st_id = ? AND us_id = ? AND cart_status_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "iis", $stock, $us_id, $status);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $cart_id);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);
    $cart = $cart_id;
    
    // If no existing cart, create a new one
    if (!$cart) {
        $sql = "INSERT INTO cart (cart_id, st_id, us_id, dept_id, cart_date, cart_time, cart_status_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiisss", $null, $stock, $us_id, $dept_id, $date, $time, $status);
        mysqli_stmt_execute($stmt);
        $cart = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }

    // Check if the product is already in the cart_detail
    $check_product_sql = "SELECT COUNT(*) FROM cart_detail WHERE cart_id = ? AND prod_id = ? AND cart_status_id = ?";
    $check_product_stmt = mysqli_prepare($conn, $check_product_sql);
    mysqli_stmt_bind_param($check_product_stmt, "iis", $cart, $prod_id, $status);
    mysqli_stmt_execute($check_product_stmt);
    mysqli_stmt_bind_result($check_product_stmt, $product_count);
    mysqli_stmt_fetch($check_product_stmt);
    mysqli_stmt_close($check_product_stmt);

    if ($product_count > 0) {
        $data_json = array("status" => "error", "message" => "Product already in the cart.");
        echo json_encode($data_json);
        exit();
    }

    // Add to cart_detail
    $sql2 = "INSERT INTO cart_detail (cart_detail_id, cart_id, prod_id, cart_amount, cart_detail, cart_status_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "iiiiss", $null, $cart, $prod_id, $amount, $detail, $status);
    $result2 = mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    if ($result2) {
        $data_json = array("status" => "successfully");
    } else {
        $data_json = array("status" => "error", "message" => "Failed to add cart.");
    }
} else {
    $data_json = array("status" => "error", "message" => "Product ID not found.");
}

echo json_encode($data_json);
mysqli_close($conn);
?>
