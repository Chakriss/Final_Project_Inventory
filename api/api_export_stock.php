<?php
session_start();
include_once('../config/connect_db.php');

$stock = $_SESSION["user_stock"];

// Fetch data from the database
if($stock == 1){
    $query = "SELECT product.prod_id, product.prod_name, product.prod_amount, product.prod_amount_min, product.prod_price, product.prod_unit, prod_detail, product_type.prod_type_desc
    FROM product
    LEFT JOIN product_type ON product.prod_type_id = product_type.prod_type_id
    WHERE product.st_id = '1'"; // Specify the table name here
} else {
    $query = "SELECT product.prod_id, product.prod_name, product.prod_amount, product.prod_amount_min, product.prod_price, product.prod_unit, prod_detail, product_type.prod_type_desc 
    FROM product
    LEFT JOIN product_type ON product.prod_type_id = product_type.prod_type_id
    WHERE product.st_id = '2'"; // Specify the table name here
}


$result = $conn->query($query);
$data = [];

// Check if the query was successful
if ($result) {
    // Output each row as an array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data.']);
}

mysqli_close($conn);
