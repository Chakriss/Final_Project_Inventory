<?php
include_once('../config/connect_db.php');
$data_json = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_detail_id = $_POST['cart_detail_id'];
    $cart_amount = $_POST['cart_amount'];

    // Step 1: ค้นหาจำนวนสินค้าที่มีอยู่ในฐานข้อมูลจาก product table
    $prod_id_sql = "SELECT prod_id FROM cart_detail WHERE cart_detail_id = ?";
    $prod_stmt = $conn->prepare($prod_id_sql);
    $prod_stmt->bind_param("i", $cart_detail_id);
    $prod_stmt->execute();
    $prod_stmt->bind_result($prod_id);
    $prod_stmt->fetch();
    $prod_stmt->close();

    $stock_sql = "SELECT prod_amount FROM product WHERE prod_id = ?";
    $stock_stmt = $conn->prepare($stock_sql);
    $stock_stmt->bind_param("i", $prod_id);
    $stock_stmt->execute();
    $stock_stmt->bind_result($prod_amount);
    $stock_stmt->fetch();
    $stock_stmt->close();

    // Step 2: ตรวจสอบว่าจำนวนในตะกร้าไม่เกินจำนวนในสต็อก
    if ($cart_amount > $prod_amount) {
        $data_json = array("status" => "error", "message" => "Insufficient product quantity.");
    } else {
        // Step 3: อัปเดตจำนวนสินค้าในตะกร้า
        $sql = "UPDATE cart_detail SET cart_amount = ? WHERE cart_detail_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cart_amount, $cart_detail_id);

        if ($stmt->execute()) {
            $data_json = array("status" => "successfully");
        } else {
            $data_json = array("status" => "error", "message" => "Failed to update cart.");
        }
    }

    echo json_encode($data_json);
}

mysqli_close($conn);
