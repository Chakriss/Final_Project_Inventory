<?php
include_once('../config/connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_detail_id = $_POST['cart_detail_id'];
    $cart_amount = $_POST['cart_amount'];

    // เชื่อมต่อฐานข้อมูลและทำการอัพเดตจำนวนสินค้าในตะกร้า
    $sql = "UPDATE cart_detail SET cart_amount = ? WHERE cart_detail_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_amount, $cart_detail_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
