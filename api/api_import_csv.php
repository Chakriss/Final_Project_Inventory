<?php
session_start();
include_once('../config/connect_db.php');
header('Content-Type: application/json');
// Set the timezone to Bangkok
date_default_timezone_set('Asia/Bangkok');
$status = 'A';
$date = date('Y-m-d');
$img = 'no_img.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // ตรวจสอบประเภทของไฟล์ CSV
    if ($file['type'] !== 'application/vnd.ms-excel' && !preg_match('/\.csv$/', $file['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Please upload a CSV file only.']);
        exit;
    }

    // อ่านไฟล์ CSV และตรวจสอบว่ามีการอัพโหลดไฟล์สำเร็จหรือไม่
    $fileTmpPath = $file['tmp_name'];
    $handle = fopen($fileTmpPath, 'r');
    if ($handle === false) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot open uploaded file.']);
        exit;
    }

    // เริ่มอ่านไฟล์ CSV
    $data = [];
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // ตรวจสอบจำนวน column ของ CSV
        if (count($row) < 7) { // ปรับตามจำนวนฟิลด์ที่ต้องการ
            continue;
        }

        // จัดเก็บข้อมูลใน array
        $data[] = [
            'name' => $row[0],
            'amount' => $row[1],
            'amount_min' => $row[2],
            'price' => $row[3],
            'unit' => $row[4],
            'detail' => $row[5],
            'type' => $row[6],
        ];
    }
    fclose($handle);

    // เริ่ม transaction
    mysqli_begin_transaction($conn);
    $inserted = 0;
    $has_error = false; // Flag เพื่อตรวจสอบว่าเกิดข้อผิดพลาดหรือไม่

    foreach ($data as $product) {
        // ตรวจสอบประเภทสินค้า
        $check = "SELECT prod_type_id FROM product_type WHERE prod_type_desc = ? AND st_id = ?";
        $stmt1 = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt1, "si", $product['type'], $_SESSION["user_stock"]);
        mysqli_stmt_execute($stmt1);
        $result = mysqli_stmt_get_result($stmt1);

        // ตรวจสอบว่าได้ผลลัพธ์กลับมาหรือไม่
        if ($row = mysqli_fetch_assoc($result)) {
            $type_id = $row['prod_type_id'];
        } else {
            $type_id = null; // กำหนดค่าเป็น null ถ้าไม่พบประเภท
        }
        mysqli_stmt_close($stmt1);

        // Check for duplicate product name
        $duplicate_check_sql = "SELECT COUNT(*) FROM product WHERE prod_name = ? AND st_id = ?";
        $duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
        mysqli_stmt_bind_param($duplicate_stmt, "si", $product['name'], $_SESSION["user_stock"]);
        mysqli_stmt_execute($duplicate_stmt);
        mysqli_stmt_bind_result($duplicate_stmt, $count);
        mysqli_stmt_fetch($duplicate_stmt);
        mysqli_stmt_close($duplicate_stmt);

        if ($count > 0) {
            $data_json = array('status' => 'error', 'message' => 'This product is already in stock.');
            echo json_encode($data_json);
            $has_error = true; // ตั้งค่าธงว่าเกิดข้อผิดพลาด
            break; // ออกจากลูป
        }

        // แทรกข้อมูลเข้าสู่ฐานข้อมูล
        $sql = "INSERT INTO product (prod_name, prod_amount, prod_amount_min, prod_price, prod_unit, prod_detail, prod_date, st_id, prod_type_id, prod_status, prod_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // ตรวจสอบว่า $type_id ไม่เป็น null ก่อนทำการ bind
        if ($type_id !== null) {
            mysqli_stmt_bind_param($stmt, "siidsssiiss", $product['name'], $product['amount'], $product['amount_min'], $product['price'], $product['unit'], $product['detail'], $date, $_SESSION["user_stock"], $type_id, $status, $img);
            if (mysqli_stmt_execute($stmt)) {
                $inserted++;
            } else {
                $has_error = true; // ตั้งค่าธงว่าเกิดข้อผิดพลาด
            }
        }
        mysqli_stmt_close($stmt);
    }

    // ตรวจสอบข้อผิดพลาดและทำการ commit หรือ rollback
    if ($has_error) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Failed to add products. Transaction rolled back.']);
    } else {
        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'message' => "Successfully added $inserted products from CSV."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or invalid request.']);
}
