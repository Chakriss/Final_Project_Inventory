<?php
session_start();

include_once('../config/connect_db.php');
$stock = $_SESSION["user_stock"];
$us_id = $_SESSION["user_id"];
$products = isset($_POST['products']) ? $_POST['products'] : null; // Check if products are set
$date = $_POST['date'];
$time = $_POST['time'];

$data_json = array();
$error_messages = [];

// Check if products are provided
if (is_null($products) || empty($products)) {
    $data_json = ["status" => "error", "message" => "No products provided."];
    header('Content-Type: application/json');
    echo json_encode($data_json);
    exit; // Terminate the script
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Insert into receive_product (header)
    $receive_sql = "INSERT INTO receive_product (rec_id, rec_date, rec_time, us_id, st_id) VALUES (?, ?, ?, ?, ?)";
    $receive_stmt = mysqli_prepare($conn, $receive_sql);
    mysqli_stmt_bind_param($receive_stmt, "issii", $null, $date, $time, $us_id, $stock);
    if (!mysqli_stmt_execute($receive_stmt)) {
        throw new Exception("Failed to insert into receive_product: " . mysqli_stmt_error($receive_stmt));
    }
    mysqli_stmt_close($receive_stmt);

    // Retrieve the rec_id for use in receive_detail
    $check_sql = "SELECT max(rec_id) as rec_id FROM receive_product WHERE st_id = ? AND us_id = ? AND rec_date = ? AND rec_time = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "iiss", $stock, $us_id, $date, $time);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $receive_id);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    if (!$receive_id) {
        throw new Exception("Failed to retrieve receive ID.");
    }

    // Process each product entry
    foreach ($products as $product) {
        if (isset($product['product_id']) && isset($product['amount'])) {
            $product_id = intval($product['product_id']);
            $amount = intval($product['amount']);
            $price = intval($product['price']);

            // Validate the inputs
            if ($product_id <= 0 || $amount <= 0) {
                $error_messages[] = "Invalid data for product ID $product_id";
                continue;
            }

            //ดึง prod_name มา
            $stock_sql = "SELECT prod_name FROM product WHERE prod_id = ?";
            $stock_stmt = mysqli_prepare($conn, $stock_sql);
            mysqli_stmt_bind_param($stock_stmt, "i", $product_id);
            mysqli_stmt_execute($stock_stmt);
            mysqli_stmt_bind_result($stock_stmt, $prod_name);
            mysqli_stmt_fetch($stock_stmt);
            mysqli_stmt_close($stock_stmt);

            // Insert into receive_product_detail (details)
            $receive_detail_sql = "INSERT INTO receive_product_detail (rec_detail_id, rec_id, prod_id, prod_name, rec_amount) VALUES (?, ?, ?, ?, ?)";
            $receive_detail_stmt = mysqli_prepare($conn, $receive_detail_sql);
            mysqli_stmt_bind_param($receive_detail_stmt, "iiisi", $null, $receive_id, $product_id, $prod_name, $amount);
            if (!mysqli_stmt_execute($receive_detail_stmt)) {
                throw new Exception("Failed to insert into receive_product_detail for product ID $product_id: " . mysqli_stmt_error($receive_detail_stmt));
            }
            mysqli_stmt_close($receive_detail_stmt);
            
            // Update product price in the product table
            $update_price_sql = "UPDATE product SET prod_price = (prod_price + ?) / 2 WHERE prod_id = ?";
            $update_price_stmt = mysqli_prepare($conn, $update_price_sql);
            mysqli_stmt_bind_param($update_price_stmt, "ii", $price, $product_id);
            if (!mysqli_stmt_execute($update_price_stmt)) {
                throw new Exception("Failed to update product price for product ID $product_id: " . mysqli_stmt_error($update_price_stmt));
            }
            mysqli_stmt_close($update_price_stmt);

            // Update product amount in the product table
            $update_product_sql = "UPDATE product SET prod_amount = prod_amount + ? WHERE prod_id = ?";
            $update_product_stmt = mysqli_prepare($conn, $update_product_sql);
            mysqli_stmt_bind_param($update_product_stmt, "ii", $amount, $product_id);
            if (!mysqli_stmt_execute($update_product_stmt)) {
                throw new Exception("Failed to update product amount for product ID $product_id: " . mysqli_stmt_error($update_product_stmt));
            }
            mysqli_stmt_close($update_product_stmt);
        } else {
            $error_messages[] = "Product ID or amount missing";
        }
    }

    // Commit the transaction if everything was successful
    if (empty($error_messages)) {
        mysqli_commit($conn);
        $data_json = ["status" => "success", "message" => "Products successfully processed"];
    } else {
        throw new Exception(implode("; ", $error_messages));
    }
} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($conn);
    $data_json = ["status" => "error", "message" => $e->getMessage()];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($data_json);

mysqli_close($conn);
