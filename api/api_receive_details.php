<?php
include_once '../config/connect_db.php'; // Include your database connection

if (isset($_GET['order_id'])) {
    $rec_id = $_GET['order_id'];

    // Fetch order details from the database
    $query = "SELECT product.prod_name,
                     receive_product_detail.rec_amount
              FROM receive_product_detail
              LEFT JOIN product ON receive_product_detail.prod_id = product.prod_id
              WHERE receive_product_detail.rec_id = ?";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $rec_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Start building the order details HTML
            echo '<table class="table table-striped table-hover">';
            echo '<thead><tr><th style="text-align: center;">Product</th><th style="text-align: center;">Amount</th></tr></thead>';
            echo '<tbody>';
            while ($detail = $result->fetch_assoc()) {

                echo '<tr>';
                echo '<td align="center">' . htmlspecialchars($detail['prod_name']) . '</td>';
                echo '<td align="center">' . htmlspecialchars($detail['rec_amount']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No order details found for this order.</p>';
        }

        // Close statement
        $stmt->close();
    } else {
        echo '<p>Error preparing the query: ' . htmlspecialchars($conn->error) . '</p>';
    }
}

// Close the database connection
$conn->close();
?>
