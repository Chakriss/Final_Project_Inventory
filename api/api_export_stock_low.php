<?php
session_start();
include_once('../config/connect_db.php');

$stock = $_SESSION["user_stock"];

// Prepare the SQL query
$query = "SELECT * FROM product WHERE st_id = ? AND prod_amount <= prod_amount_min";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $stock);

// Execute the prepared statement
if (mysqli_stmt_execute($stmt)) {
    // Get the result set from the prepared statement
    $result = mysqli_stmt_get_result($stmt);
    $data = [];

    // Output each row as an array
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data.']);
}

// Close the prepared statement and the database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
