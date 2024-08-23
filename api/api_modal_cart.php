<?php

include_once('../config/connect_db.php');

if (isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];

    // Fetch product details from the database based on prod_id
    $query = "SELECT prod_id, prod_name, prod_unit, prod_img FROM product WHERE prod_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Ensure that prod_img is not empty
        if (empty($product['prod_img'])) {
            $product['prod_img'] = 'no_img.jpg'; // Set a default image if no image is provided
        }

        echo json_encode($product); // Return product details as JSON
    } else {
        echo json_encode(['error' => 'Product not found']);
    }

    $stmt->close();
}
?>
