<?php
include_once('../config/connect_db.php');

$us_id = $_POST['us_id'];

$sql = "DELETE FROM user WHERE us_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $us_id);
$result = mysqli_stmt_execute($stmt);


if ($result) {
    $data_json = array("status" => "The type has been successfully deleted.", "color" => "success");
} else {
    $data_json = array("status" => "Delete Error", "color" => "error");
}


echo json_encode($data_json);
mysqli_close($conn);