<?php
include_once('../config/connect_db.php');

$dept_id = $_POST['dept_id'];



$sql = "DELETE FROM department WHERE dept_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $dept_id);
$result = mysqli_stmt_execute($stmt);


if ($result) {
    $data_json = array("status" => "The type has been successfully deleted.", "color" => "success");
} else {
    $data_json = array("status" => "Delete Error", "color" => "error");
}


echo json_encode($data_json);
mysqli_close($conn);
