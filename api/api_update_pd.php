<?php

session_start();

include_once('../config/connect_db.php');
$stock = $_SESSION["user_stock"];

$id = $_POST['id'];
$name = $_POST['name'];
$amount = $_POST['amount'];
$amount_min = $_POST['amount_min'];
$price = $_POST['price'];
$unit = $_POST['unit'];
$type = $_POST['type'];
$status = $_POST['status'];

$data_json = array();


if (isset($_POST['id'])) {

    // Check for duplicate product name
    $duplicate_check_sql = "SELECT COUNT(*) FROM product WHERE prod_name = ? AND st_id = ?";
    $duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
    mysqli_stmt_bind_param($duplicate_stmt, "si", $name, $stock);
    mysqli_stmt_execute($duplicate_stmt);
    mysqli_stmt_bind_result($duplicate_stmt, $count);
    mysqli_stmt_fetch($duplicate_stmt);
    mysqli_stmt_close($duplicate_stmt);

    if ($count > 0) {
        $data_json = array("status" => "error", "message" => "มีสินค้าชื่อนี้อยู่แล้ว");
        echo json_encode($data_json);
        exit();
    }

    if (isset($_FILES['img']['name']) && $_FILES['img']['name'] != '') {
        // ดึงชื่อรูปเก่าจากฐานข้อมูลก่อนอัพโหลดรูปใหม่
        $sql_img = "SELECT prod_img FROM product WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql_img);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $old_img);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $img_name = $_FILES['img']['name'];
        $img_tmp_name = $_FILES['img']['tmp_name'];

        // ตรวจสอบว่าชื่อไฟล์เป็น no_img.jpg หรือไม่
        if ($img_name != 'no_img.jpg') {
            // Extract the file extension
            $typefile = strrchr($img_name, ".");
            // สร้างวันที่เพื่อมาใช้ในการเปลี่ยนชื่อไฟล์รูปภาพเพื่อไม่ให้ชื่อไฟล์ซ้ำกัน
            $date1 = date("Ymd_His");
            // สุ่มตัวเลขมาเพื่อมาใช้ในการเปลี่ยนชื่อไฟล์รูปภาพเพื่อไม่ให้ชื่อไฟล์ซ้ำกัน
            $numrand = mt_rand();

            // สร้างชื่อไฟล์ใหม่
            $img_newname = $numrand . $date1 . $typefile;
            $img = 'photo/' . $img_newname;
            $prod_img = $img_newname;
        } else {
            // ถ้าเป็น no_img.jpg ให้ใช้ชื่อไฟล์เดิม
            $img = 'photo/' . $img_name;
            $prod_img = $img_name;
        }

        // Move the uploaded file to the photo directory
        if (!move_uploaded_file($img_tmp_name, '../' . $img)) {
            $errors[] = "Image upload failed for product '$name'.";
        }

        // ลบรูปเก่าถ้าไม่ใช่ 'no_img.jpg'
        if ($old_img && $old_img != 'no_img.jpg' && file_exists('../photo/' . $old_img)) {
            unlink('../photo/' . $old_img);
        }
    } else {
        // ถ้าไม่มีการอัพโหลดรูปใหม่ ให้นำชื่อรูปเก่ามาใช้
        $sql1 = "SELECT prod_img FROM product WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt, "i", $id_hr);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $prod_img);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }


    if ($prod_img) {
        $sql = "UPDATE product SET prod_name = ?, prod_amount = ?, prod_amount_min = ?, prod_price = ?, prod_unit = ?, prod_type_id = ?, prod_status = ?, prod_img = ? WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siiisissi", $name, $amount, $amount_min, $price, $unit, $type, $status, $prod_img, $id);
    } else {
        $sql = "UPDATE product SET prod_name = ?, prod_amount = ?, prod_amount_min = ?, prod_price = ?, prod_unit = ?, prod_type_id = ?, prod_status = ? WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siiisisi", $name, $amount, $amount_min, $price, $unit, $type, $status, $id);
    }

    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $data_json = array("status" => "successfully");
    } else {
        $data_json = array("status" => "error", "message" => "เพิ่มสินค้าลงฐานข้อมูลผิดพลาด");
    }
} else {
    $data_json = array("status" => "error", "message" => "ไม่พบรหัสสินค้า");
}

echo json_encode($data_json);

mysqli_stmt_close($stmt);
mysqli_close($conn);
