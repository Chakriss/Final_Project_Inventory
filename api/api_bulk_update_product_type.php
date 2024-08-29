<?php
include_once('../config/connect_db.php');

if (isset($_POST['prod_ids']) && isset($_POST['status'])) {
    $prod_ids = $_POST['prod_ids'];
    $status = $_POST['status'];

    // Convert the array of IDs into a string for the SQL query
    $prod_ids_string = implode(",", array_map('intval', $prod_ids));

    $sql = "UPDATE product_type SET prod_status = ? WHERE prod_type_id IN ($prod_ids_string)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);

    if ($stmt->execute()) {
        echo json_encode(array("status" => "successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to update product type."));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request."));
}
?>
