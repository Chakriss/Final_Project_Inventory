<?php
session_start();
include_once('../config/connect_db.php');

$stock = $_SESSION["user_stock"];

// Prepare the SQL query based on user stock
if ($stock == 3) {
    // Check stock levels 1 and 2
    $query = "SELECT * FROM product WHERE st_id IN (1, 2) AND prod_amount <= prod_amount_min";
} else {
    // Check only the current stock level
    $query = "SELECT * FROM product WHERE st_id = ? AND prod_amount <= prod_amount_min";
}

// Prepare the statement
$stmt = mysqli_prepare($conn, $query);

// Bind parameters if the stock is not 3
if ($stock != 3) {
    mysqli_stmt_bind_param($stmt, "i", $stock);
}

// Execute the prepared statement
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data.']);
}

// Close the prepared statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
