<?php

session_start();

include_once('../config/connect_db.php');
header('Content-Type: application/json');   

$stock = $_SESSION["user_stock"];
$data_json = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Extract and sanitize POST data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
    $amount_min = isset($_POST['amount_min']) ? intval($_POST['amount_min']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $unit = isset($_POST['unit']) ? trim($_POST['unit']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $img = isset($_FILES['img']) ? $_FILES['img'] : null;

    // Check if required fields are filled
    if (empty($name) || empty($amount) || empty($amount_min) || empty($price) || empty($unit) || empty($type) || empty($status)) {
        $data_json = array('status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบทุกช่อง');
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
        $data_json = array('status' => 'error', 'message' => 'สินค้านี้มีอยู่แล้วในคลัง');
        echo json_encode($data_json);
        exit;
    }

    // Handle image upload if an image file is provided
    $prod_img = 'no_img.jpg'; // Default image
    if ($img && $img['error'] == 0) {
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        if (in_array($img['type'], $allowed_types)) {
            $img_extension = pathinfo($img['name'], PATHINFO_EXTENSION);
            $new_img_name = uniqid() . "_" . time() . "." . $img_extension;
            $upload_dir = '../photo/'; // Define the upload directory
            $upload_path = $upload_dir . $new_img_name;

            if (move_uploaded_file($img['tmp_name'], $upload_path)) {
                $prod_img = $new_img_name;
            }
        }
    }

    // Insert product into the database
    $insert_sql = "INSERT INTO product (prod_name, prod_amount, prod_amount_min, prod_price, prod_unit, prod_type_id, prod_status, prod_img, st_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "siidssssi", $name, $amount, $amount_min, $price, $unit, $type, $status, $prod_img, $stock);

    if (mysqli_stmt_execute($insert_stmt)) {
        $data_json = array('status' => 'successfully', 'message' => 'เพิ่มสินค้าสำเร็จ');
    } else {
        $data_json = array('status' => 'error', 'message' => 'เพิ่มสินค้าผิดพลาด');
    }

    mysqli_stmt_close($insert_stmt);
    echo json_encode($data_json);
}
?>