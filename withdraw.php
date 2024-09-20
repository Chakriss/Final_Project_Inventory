<?php

session_start();
// include_once 'config/connect_db.php';
include_once 'config/function.php';


// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {
    $stock = $_SESSION["user_stock"];

    $result_order_head = orderHead($conn, $stock);


?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Optinova</title>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <link rel="stylesheet" href="assets/vendors/fontawesome/all.min.css">

        <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
        <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
        <link rel="stylesheet" href="assets/vendors/choices.js/choices.min.css" />
        <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="assets/css/app.css">
        <link rel="shortcut icon" href="assets/images/logo/optinova.jpg" type="image/x-icon">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    </head>


    <?php
    include_once 'menu_admin.php';
    include_once 'navbar.php';
    ?>


    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3 class="mb-2">Confirm Order</h3>
                        <!-- <p class="mb-0">Click on the order to approve or disapprove it.</p> -->
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                    <p class="mb-2">Click on the order to approve or disapprove it.  คลิกที่คำสั่งซื้อเพื่ออนุมัติหรือไม่อนุมัติ</p>   
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Order ID</th>
                                    <th style="text-align: center;">User Name</th>
                                    <th style="text-align: center;">Department</th>
                                    <th style="text-align: center;">Date</th>
                                    <th style="text-align: center;">Time</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_order_head->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['cart_id']; ?>" class="order-row" data-order-id="<?php echo $row['cart_id']; ?>">
                                        <td align="center"><?php echo $row['cart_id']; ?></td>
                                        <td align="center"><?php echo $row['us_name']; ?></td>
                                        <td align="center"><?php echo $row['dept_name']; ?></td>
                                        <td align="center"><?php echo $row['cart_date']; ?></td>
                                        <td align="center"><?php echo $row['cart_time']; ?></td>
                                        <td align="center">
                                            <?php
                                            // Determine the badge class based on the cart_status value
                                            switch ($row['cart_status']) {
                                                case 'Pending':
                                                    $badge_class = 'badge bg-warning';
                                                    break;
                                                case 'Approved':
                                                    $badge_class = 'badge bg-success';
                                                    break;
                                                case 'Reject':
                                                    $badge_class = 'badge bg-danger';
                                                    break;
                                                default:
                                                    $badge_class = 'badge bg-secondary'; // Default class if status is unknown
                                                    break;
                                            }
                                            ?>
                                            <span class="<?php echo $badge_class; ?>"><?php echo $row['cart_status']; ?></span>
                                        </td>

                                    </tr>
                                <?php endwhile ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>


        <!-- ขยายรูปออกมาเป็น Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <img id="imageModalSrc" src="" class="img-fluid" alt="Expanded Image">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ขยายรูปออกมาเป็น Modal -->


        <!-- Order Details Modal -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white" id="orderDetailsModalLabel">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="orderDetailsContent">
                        <!-- Order details will be loaded here dynamically -->
                    </div>
                    <div class="modal-footer">
                        <!-- Buttons to update status -->
                        <div class="d-flex justify-content-between w-100">
                            <div>
                                <button type="button" class="btn btn-primary confirm-order-btn">Comfirm Order</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-success allow-btn" data-status="Approved">Allow All</button>
                                <button type="button" class="btn btn-danger deny-btn" data-status="Reject">Deny All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order Details Modal -->


        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>Develop by</p>
                </div>
                <div class="float-end">
                    <p><span class="text-danger"><i class="bi bi-heart"></i> </span>Chakris Pantuwech <span class="text-danger"><i class="bi bi-heart"></i></p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/fontawesome/all.min.js"></script>
    <script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendors/choices.js/choices.min.js"></script>
    <script>
        // Simple Datatable
        let table1 = document.querySelector('#table1');
        let dataTable = new simpleDatatables.DataTable(table1);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
    <script src="assets/js/main.js"></script>



    <script>
        //modal  order_detail
        $(document).ready(function() {
            // Handle the row click to load order details
            
            $('#table1').on('click', '.order-row', function() {
                var orderId = $(this).data('order-id'); // Get the order ID from the row's data attribute

                // Make an AJAX request to fetch order details
                $.ajax({
                    url: '/Final_Project/api/api_order_details.php', // PHP file that handles fetching order details
                    method: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        // Load the response into the modal content area
                        $('#orderDetailsContent').html(response);

                        // Store the order ID in a data attribute for later use
                        $('#orderDetailsModal').data('order-id', orderId);

                        // Show the modal
                        $('#orderDetailsModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load order details:', error);
                        alert('Failed to load order details.');
                    }
                });
            });

            // Handle the Allow and Deny button clicks //เปลี่ยนทั้งหมด
            $(document).on('click', '.allow-btn, .deny-btn', function() {
                var status = $(this).hasClass('allow-btn') ? 'Approved' : 'Reject'; // Determine the status
                var orderId = $('#orderDetailsModal').data('order-id'); // Get the stored order ID

                // Send an AJAX request to update the order status
                $.ajax({
                    url: '/Final_Project/api/api_update_order_status.php', // PHP file to update the status
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        status: status
                    },
                    dataType: 'json', // Ensure jQuery interprets the response as JSON
                    success: function(response) {
                        if (response.status === "success") {
                            // Reload the order details in the modal to reflect the updated status
                            $.ajax({
                                url: '/Final_Project/api/api_order_details.php',
                                method: 'GET',
                                data: {
                                    order_id: orderId
                                },
                                success: function(response) {
                                    $('#orderDetailsContent').html(response);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Failed to reload order details:', error);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to update order status:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to update order status.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });


            // Handle the Allow and Deny button clicks เปลี่ยนชิ้นเดียว
            $(document).on('click', '.allow-btn-1, .deny-btn-1', function() {
                var status = $(this).hasClass('allow-btn-1') ? 'Approved' : 'Reject'; // Determine the status
                var cartDetailId = $(this).data('cart-detail-id'); // Get the cart_detail_id for the specific item

                // Send an AJAX request to update the specific item's status
                $.ajax({
                    url: '/Final_Project/api/api_update_order_status_one.php', // PHP file to update the status
                    method: 'POST',
                    data: {
                        cart_detail_id: cartDetailId,
                        status: status
                    },
                    dataType: 'json', // Ensure jQuery interprets the response as JSON
                    success: function(response) {
                        if (response.status === "success") {
                            // Reload the order details in the modal to reflect the updated status
                            $.ajax({
                                url: '/Final_Project/api/api_order_details.php',
                                method: 'GET',
                                data: {
                                    order_id: $('#orderDetailsModal').data('order-id') // Use the stored order ID
                                },
                                success: function(response) {
                                    $('#orderDetailsContent').html(response);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Failed to reload order details:', error);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to update order status:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to update order status.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });






            // Handle the Confirm Order button click
            $('.confirm-order-btn').on('click', function() {
                var orderId = $('#orderDetailsModal').data('order-id'); // Get the stored order ID

                // Send an AJAX request to update the cart status
                $.ajax({
                    url: '/Final_Project/api/api_update_cart_status.php', // PHP file to update the cart status
                    method: 'POST',
                    data: {
                        order_id: orderId
                    },
                    dataType: 'json', // Ensure jQuery interprets the response as JSON
                    success: function(response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Order Confirmed',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                $('#orderDetailsModal').modal('hide'); // Hide the modal
                                location.reload(); // Refresh the page or use other methods to update the list
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error); // Log error details
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to confirm the order.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });


    </script>

<?php
} else {
    header("location: error_user_page.php");
}
?>