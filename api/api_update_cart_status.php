<?php
include_once '../config/connect_db.php'; // Include your database connection

header('Content-Type: application/json'); // Set content type to JSON

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $data_json = array();

    // เริ่มต้นการทำธุรกรรม
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Prepare the SQL query to get cart status
        $sql_check = "SELECT cart_status_id, prod_id, cart_amount FROM cart_detail WHERE cart_id = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "i", $order_id);
        mysqli_stmt_execute($stmt_check);
        $result = mysqli_stmt_get_result($stmt_check);

        // Step 2: Initialize flags for status checking
        $hasApproved = false;
        $hasReject = false;
        $isAllApproved = true;
        $isAllRejected = true;

        // Step 3: Iterate through the results and set flags
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['cart_status_id'] == 'A') {
                $hasApproved = true;
                $isAllRejected = false; // There is at least one approved, so it can't be all rejected
            } elseif ($row['cart_status_id'] == 'R') {
                $hasReject = true;
                $isAllApproved = false; // There is at least one rejected, so it can't be all approved

                // Step 4: Update the product's prod_amount by returning the cart_amount
                $sql_update_prod = "UPDATE product SET prod_amount = prod_amount + ? WHERE prod_id = ?";
                $stmt_update_prod = mysqli_prepare($conn, $sql_update_prod);
                mysqli_stmt_bind_param($stmt_update_prod, "ii", $row['cart_amount'], $row['prod_id']);
                if (!mysqli_stmt_execute($stmt_update_prod)) {
                    throw new Exception("Failed to update product stock.");
                }
                mysqli_stmt_close($stmt_update_prod); // Close this statement after execution
            }
        }

        // Step 5: Determine the result based on flags
        if ($isAllApproved || ($hasApproved && $hasReject)) {
            $result = 1;
        } elseif ($isAllRejected) {
            $result = 0;
        } else {
            $result = -1; // Or handle this case as needed
        }

        // Update cart status based on result
        if ($result == 1) {
            $status = 'A';
            $sql = "UPDATE cart SET cart_status_id = ? WHERE cart_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update cart status.");
            }

            $data_json = array("status" => "success", "message" => "The product has been approved.");
        } else if ($result == 0) {
            $status = 'R';
            $sql = "UPDATE cart SET cart_status_id = ? WHERE cart_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update cart status.");
            }

            $data_json = array("status" => "success", "message" => "The product has been rejected, and stock has been returned.");
        } else {
            $data_json = array("status" => "error", "message" => "Please approve or reject the product first.");
        }

        // ถ้าไม่มีข้อผิดพลาด ให้ commit การทำธุรกรรม
        mysqli_commit($conn);
    } catch (Exception $e) {
        // ถ้ามีข้อผิดพลาด ให้ rollback การทำธุรกรรม
        mysqli_rollback($conn);
        $data_json = array("status" => "error", "message" => "Transaction failed: " . $e->getMessage());
    }

    // Send JSON response
    echo json_encode($data_json);

    // Close the statement and database connection
    mysqli_stmt_close($stmt_check);
    mysqli_close($conn);
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request."));
}
?>
