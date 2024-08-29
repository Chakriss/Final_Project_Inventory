<?php
include_once('../config/connect_db.php');

// Set the timezone to Bangkok
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d');
$time = date('H:i:s');

if (isset($_POST['code']) && isset($_POST['cart_id']) && isset($_POST['dept'])) {
    $code = $_POST['code'];
    $cart_id = $_POST['cart_id'];
    $dept = $_POST['dept'];
    $cart_status = 'P';  // Assuming 'P' stands for the status code for 'confirmed'

    if ($code == 'xxx') {
        // Start a transaction
        mysqli_begin_transaction($conn);

        try {
            // Update the cart status and date/time
            $cart_sql = "UPDATE cart SET cart_date = ?, cart_time = ?, cart_status_id = ?, dept_id = ? WHERE cart_id = ?";
            $cart_stmt = mysqli_prepare($conn, $cart_sql);
            mysqli_stmt_bind_param($cart_stmt, "sssii", $date, $time, $cart_status, $dept, $cart_id);

            if (!mysqli_stmt_execute($cart_stmt)) {
                throw new Exception("Failed to update cart.");
            }

            // Get all cart_detail items for the given cart_id
            $cart_detail_sql = "SELECT cart_detail_id, cart_amount, prod_id FROM cart_detail WHERE cart_id = ?";
            $cart_detail_stmt = mysqli_prepare($conn, $cart_detail_sql);
            mysqli_stmt_bind_param($cart_detail_stmt, "i", $cart_id);
            mysqli_stmt_execute($cart_detail_stmt);
            $cart_detail_result = mysqli_stmt_get_result($cart_detail_stmt);

            while ($cart_item = mysqli_fetch_assoc($cart_detail_result)) {
                $cart_detail_id = $cart_item['cart_detail_id'];
                $cart_amount = $cart_item['cart_amount'];
                $prod_id = $cart_item['prod_id'];

                // Get the product amount from the product table
                $stock_sql = "SELECT prod_amount FROM product WHERE prod_id = ?";
                $stock_stmt = mysqli_prepare($conn, $stock_sql);
                mysqli_stmt_bind_param($stock_stmt, "i", $prod_id);
                mysqli_stmt_execute($stock_stmt);
                mysqli_stmt_bind_result($stock_stmt, $prod_amount);
                mysqli_stmt_fetch($stock_stmt);
                mysqli_stmt_close($stock_stmt);

                // Calculate the new stock amount
                $new_stock_amount = $prod_amount - $cart_amount;

                // Check if stock is sufficient
                if ($new_stock_amount < 0) {
                    throw new Exception("Insufficient stock for product ID $prod_id.");
                }

                // Update the product amount in the product table
                $update_stock_sql = "UPDATE product SET prod_amount = ? WHERE prod_id = ?";
                $update_stock_stmt = mysqli_prepare($conn, $update_stock_sql);
                mysqli_stmt_bind_param($update_stock_stmt, "ii", $new_stock_amount, $prod_id);

                if (!mysqli_stmt_execute($update_stock_stmt)) {
                    throw new Exception("Failed to update product stock.");
                }
                mysqli_stmt_close($update_stock_stmt);
            }

            // Update the status in cart_detail for the given cart_id
            $update_cart_detail_sql = "UPDATE cart_detail SET cart_status_id = ? WHERE cart_id = ?";
            $update_cart_detail_stmt = mysqli_prepare($conn, $update_cart_detail_sql);
            mysqli_stmt_bind_param($update_cart_detail_stmt, "si", $cart_status, $cart_id);

            if (!mysqli_stmt_execute($update_cart_detail_stmt)) {
                throw new Exception("Failed to update cart details.");
            }

            // Commit the transaction
            mysqli_commit($conn);
            $data_json = array("status" => "successfully");
        } catch (Exception $e) {
            // Rollback the transaction
            mysqli_rollback($conn);
            $data_json = array("status" => "error", "message" => $e->getMessage());
        }

        mysqli_stmt_close($cart_stmt);
        mysqli_stmt_close($update_cart_detail_stmt);
    }
}

echo json_encode($data_json);
mysqli_close($conn);
