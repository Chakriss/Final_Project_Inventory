<?php
include_once '../config/connect_db.php'; // Include your database connection

if (isset($_GET['order_id'])) {
    $cart_id = $_GET['order_id'];

    // Fetch order details from the database
    $query = "SELECT cart_detail.cart_id,
                     cart_detail.cart_detail_id,
                     cart_detail.prod_id,
                     cart_detail.prod_name,
                     cart_detail.cart_amount,
                     cart_detail.cart_detail,
                     cart_detail.cart_status_id,
                     cart_status.cart_status 
              FROM cart_detail 
              LEFT JOIN cart_status ON cart_detail.cart_status_id = cart_status.cart_status_id
              WHERE cart_detail.cart_id = ?";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Start building the order details HTML
            echo '<table class="table table-striped table-hover">';
            echo '<thead><tr><th style="text-align: center;">Product</th><th style="text-align: center;">Amount</th><th style="text-align: center;">Detail</th><th style="text-align: center;">Status</th></tr></thead>';
            echo '<tbody>';
            while ($detail = $result->fetch_assoc()) {
                // Determine the badge class based on the cart_status value
                $badge_class = 'badge bg-secondary'; // Default class
                switch ($detail['cart_status']) {
                    case 'Pending':
                        $badge_class = 'badge bg-warning';
                        break;
                    case 'Approved':
                        $badge_class = 'badge bg-success';
                        break;
                    case 'Reject':
                        $badge_class = 'badge bg-danger';
                        break;
                }

                echo '<tr>';
                echo '<td align="center">' . htmlspecialchars($detail['prod_name']) . '</td>';
                echo '<td align="center">' . htmlspecialchars($detail['cart_amount']) . '</td>';
                echo '<td align="center">' . htmlspecialchars($detail['cart_detail']) . '</td>';
                echo '<td align="center"><span class="' . $badge_class . '">' . htmlspecialchars($detail['cart_status']) . '</span></td>';
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
