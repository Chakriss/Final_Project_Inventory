<?php
include_once('../config/connect_db.php');

if (isset($_POST['prod_id'])) {
    $prod_id = $_POST['prod_id'];
    $query = "SELECT prod_img FROM product WHERE prod_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $prod_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $prod_img);
    mysqli_stmt_fetch($stmt);

    if ($prod_img) {
        echo json_encode(["status" => "success", "prod_img" => $prod_img]);
    } else {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
