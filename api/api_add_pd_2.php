<?php

session_start();

include_once('../config/connect_db.php');
header('Content-Type: application/json');

// Set the timezone to Bangkok
date_default_timezone_set('Asia/Bangkok');

$stock = $_SESSION["user_stock"];
$data_json = array();
$date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Extract and sanitize POST data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
    $amount_min = isset($_POST['amount_min']) ? intval($_POST['amount_min']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $unit = isset($_POST['unit']) ? trim($_POST['unit']) : '';
    $detail = isset($_POST['detail']) ? trim($_POST['detail']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $img = isset($_FILES['img']) ? $_FILES['img'] : null;

    // Check if required fields are filled
    if (empty($name) || empty($amount) || empty($amount_min) || empty($price) || empty($unit) || empty($detail) || empty($type) || empty($status)) {
        $data_json = array('status' => 'error', 'message' => 'Please fill in all fields.');
        echo json_encode($data_json);
        exit;
    }

    // Check for duplicate product name
    $duplicate_check_sql = "SELECT COUNT(*) FROM product WHERE prod_name = ? AND st_id = ?";
    $duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
    mysqli_stmt_bind_param($duplicate_stmt, "si", $name, $stock);
    mysqli_stmt_execute($duplicate_stmt);
    mysqli_stmt_bind_result($duplicate_stmt, $count);
    mysqli_stmt_fetch($duplicate_stmt);
    mysqli_stmt_close($duplicate_stmt);

    if ($count > 0) {
        $data_json = array('status' => 'error', 'message' => 'This product is already in stock.');
        echo json_encode($data_json);
        exit;
    }

    // Example improvement for validating and inserting data
    if ($img && $img['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        if (in_array($img['type'], $allowed_types)) {
            $img_extension = pathinfo($img['name'], PATHINFO_EXTENSION);
            $new_img_name = uniqid() . "_" . time() . "." . $img_extension;
            $upload_dir = '../photo/';
            $upload_path = $upload_dir . $new_img_name;

            if (!move_uploaded_file($img['tmp_name'], $upload_path)) {
                $prod_img = 'no_img.jpg';
            } else {
                $prod_img = $new_img_name;
            }
        } else {
            $prod_img = 'no_img.jpg';
        }
    } else {
        $prod_img = 'no_img.jpg';
    }


    // Insert product into the database
    $insert_sql = "INSERT INTO product (prod_name, prod_amount, prod_amount_min, prod_price, prod_unit, prod_type_id, prod_date, prod_status, prod_img, st_id, prod_detail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "siidsssssis", $name, $amount, $amount_min, $price, $unit, $type, $date, $status, $prod_img, $stock, $detail);

    // กรณีเพิ่มสินค้าสำเร็จ
    if (mysqli_stmt_execute($insert_stmt)) {
        $data_json = array('status' => 'successfully', 'message' => 'Add product success!');
    } else {
        $data_json = array('status' => 'error', 'message' => 'Failed to add product.');
    }

    mysqli_stmt_close($insert_stmt);
    echo json_encode($data_json);
}
