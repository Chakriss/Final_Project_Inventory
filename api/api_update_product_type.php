<?php

include_once('../config/connect_db.php');
$data_json = array();

if (isset($_POST['prod_type_id']) && isset($_POST['prod_status'])) {
    $prod_type_id = $_POST['prod_type_id'];
    $prod_status = $_POST['prod_status'];

    $stmt = $conn->prepare("UPDATE product_type SET prod_status = ? WHERE prod_type_id = ?");
    $stmt->bind_param("si", $prod_status, $prod_type_id);

    if ($stmt->execute()) {
        $data_json = array("status" => "successfully");
    } else {
        $data_json = array("status" => "error", "message" => "Failed to edit product type.");
    }

    echo json_encode($data_json);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
