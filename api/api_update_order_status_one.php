<?php
header('Content-Type: application/json');

// Include database connection
include_once '../config/connect_db.php';

// Check if cart_detail_id and status are sent via POST
if (isset($_POST['cart_detail_id']) && isset($_POST['status'])) {
    $cartDetailId = htmlspecialchars($_POST['cart_detail_id']);
    $status = htmlspecialchars($_POST['status']);

    // Map status to cart_status_id (if necessary)
    $statusId = ($status == 'Approved') ? 'A' : 'R';

    // Prepare the SQL statement with positional placeholders
    $query = "UPDATE cart_detail SET cart_status_id = ? WHERE cart_detail_id = ?";
    $stmt = $conn->prepare($query);

    // Bind the parameters (statusId and cartDetailId) to the SQL statement
    $stmt->bind_param("si", $statusId, $cartDetailId);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update cart status.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

// Close the database connection
$conn->close();
?>
