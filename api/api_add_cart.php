<?php
session_start();
include_once('../config/connect_db.php');
$data_json = array();

if (isset($_POST['id'])) {

    $prod_id = $_POST['id'];
    $amount = $_POST['amount'];
    $prod_dept = $_POST['dept'];
    $detail = $_POST['detail'];
    $stock = $_SESSION["user_stock"];
    $us_id = $_SESSION["user_id"];
    

    //ถ้าไม่มี session cart อยู่จะทำการสร้าง cart ใหม่
    if (!$_SESSION["cart"]) {
        //เพิ่ม cart ก่อนที่จะเพิ่ม cart_detail
        $sql = "INSERT INTO cart (cart_id, st_id, us_id, dept_id) VALUES (?, ?, ?, ?) ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", null, $stock, $us_id, $prod_dept);
        $result = mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $_SESSION["cart"] = $row['cart_id'];
    }

    $cart = $_SESSION["cart"];

    if ($_SESSION["cart"]) {
        //เพิ่ม cart_detail หลังจากเพิ่ม cart แล้ว
        $sql2 = "INSERT INTO cart_detail (cart_detail_id, cart_id, prod_id, cart_amount, cart_detail) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt, "iiiis", null, $cart, $prod_id, $amount, $detail);
    }
}
