<?php

session_start();

include_once('../config/connect_db.php');
$stock = $_SESSION["user_stock"];

$products = $_POST['products'];
$data_json = array();
$error_messages = [];

// Process each product entry
foreach ($products as $product) {
    if (isset($product['product_id']) && isset($product['amount'])) {
        $product_id = intval($product['product_id']);
        $amount = intval($product['amount']);

        // Validate the inputs
        if ($product_id <= 0 || $amount <= 0) {
            $error_messages[] = "Invalid data for product ID $product_id";
            continue;
        }

        // Example: Insert or update the product record in the database
        $stmt = $conn->prepare("UPDATE product SET prod_amount = prod_amount + ?, st_id = ? WHERE prod_id = ?");
        $stmt->bind_param("iii", $amount, $stock, $product_id);

        if (!$stmt->execute()) {
            $error_messages[] = "Failed to process product ID $product_id: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_messages[] = "Product ID or amount missing";
    }
}

// Prepare response
if (empty($error_messages)) {
    $data_json = ["status" => "success", "message" => "Products successfully processed"];
} else {
    $data_json = ["status" => "error", "message" => implode("; ", $error_messages)];
}

header('Content-Type: application/json');
echo json_encode($data_json);
