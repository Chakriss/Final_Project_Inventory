<?php
include_once('../config/connect_db.php');

$prod_id = $_POST['prod_id'];

// Retrieve the image file name before deleting the product
$sql_select = "SELECT prod_img FROM product WHERE prod_id='$prod_id'";
$result = $conn->query($sql_select);
$row = $result->fetch_assoc();
if ($row) {
    $img_file = $row['prod_img'];
    $img_path = '../photo/' . $img_file;
    $sql = "DELETE FROM product WHERE prod_id='$prod_id'";

    if ($conn->query($sql) === TRUE) {
        // Check if the image is not 'no_img.jpg' before attempting to delete it
        if ($img_file !== 'no_img.jpg' && file_exists($img_path)) {
            unlink($img_path);
        }
        
        $data_json = array("status" => "ข้อมูลถูกลบเรียบร้อย", "color" => "success");
    } else {
        $data_json = array("status" => "Delete Error", "color" => "error");
    }
} else {
    $data_json = array("status" => "Product not found.", "color" => "error");
}

echo json_encode($data_json);
mysqli_close($conn);
