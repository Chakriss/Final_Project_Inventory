<?php
include_once('../config/connect_db.php');

$prod_id = $_POST['prod_id'];

$sql = "DELETE FROM product WHERE prod_id='$prod_id'";

if ($conn->query($sql) === TRUE) {
    $data_json = array("status" => "ข้อมูลถูกลบเรียบร้อย", "color" => "success");
} else {
    $data_json = array("status" => "Delete Error", "color" => "error");
}

echo json_encode($data_json);
mysqli_close($conn);