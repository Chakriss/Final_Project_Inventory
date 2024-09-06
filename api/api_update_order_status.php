<?php
include_once '../config/connect_db.php'; // Include your database connection

header('Content-Type: application/json'); // Set the content type to JSON

$response = array(); // Initialize response array

if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update the order status in the database
    $query = "UPDATE cart_detail SET cart_status_id = (SELECT cart_status_id FROM cart_status WHERE cart_status = ?) WHERE cart_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Status updated successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to update status.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error preparing the query: ' . htmlspecialchars($conn->error);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
}

// Return JSON response
echo json_encode($response);
