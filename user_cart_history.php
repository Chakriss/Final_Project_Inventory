<?php

session_start();
// include_once 'config/connect_db.php';
include_once 'config/function.php';


// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 3)) {
    $us_id = $_SESSION["user_id"];

    $result_order_head_user = orderHistoryUser($conn, $us_id);


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
    include_once 'menu_user.php';
    include_once 'navbar.php';
    ?>


    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3 class="mb-2">History</h3>
                        <!-- <p class="mb-0">Click on the order to approve or disapprove it.</p> -->
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <p class="mb-2">Click on the order to Viwe. คลิกที่คำสั่งซื้อเพื่อดูรายละเอียด</p>
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
                                <?php while ($row = $result_order_head_user->fetch_assoc()) : ?>
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
            $('.order-row').on('click', function() {
                var orderId = $(this).data('order-id'); // Get the order ID from the row's data attribute

                // Make an AJAX request to fetch order details
                $.ajax({
                    url: '/Final_Project/api/api_order_details_user.php', // PHP file that handles fetching order details
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
        });



        //ขยายรูปออกมา
        function expandImage(imageSrc) {
            // Set the image source in the modal
            $('#imageModalSrc').attr('src', 'photo/' + imageSrc);
            // Show the modal
            $('#imageModal').modal('show');
        }
    </script>

<?php
} else {
    header("location: error_user_page.php");
}
?>