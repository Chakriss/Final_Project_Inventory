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

    $result_receive_history = receiveHistory($conn, $stock);

    $user_stock = $_SESSION["user_stock"];

    if ($user_stock == 1) {
        $stock_menu = 'stock_it.php';
    } else {
        $stock_menu = 'stock_hr.php';
    }
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

    <body>
        <div id="app">
            <!-- Sidebar -->
            <div id="sidebar" class="active">
                <div class="sidebar-wrapper active">
                    <div class="sidebar-header">
                        <div class="d-flex justify-content-between">
                            <div class="logo">
                                <a href="admin_page.php"><img src="assets/images/logo/logo_optinova.png" alt="Logo" style="width: 200px; height: auto;" srcset=""></a>
                            </div>
                            <div class="toggler">
                                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar-menu">
                        <ul class="menu">
                            <li class="sidebar-title">Menu</li>

                            <li class="sidebar-item">
                                <a href="admin_page.php" class='sidebar-link'> <i class="bi bi-grid-fill"></i> <span>Dashboard</span> </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="<?php echo $stock_menu ?>" class='sidebar-link'> <i class="bi bi-database"></i> <span>Product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="withdraw.php" class='sidebar-link'> <i class="bi bi-cart-check-fill"></i> </i> <span>Withdraw</span> <span id="withdraw_count"></span></a>
                            </li>

                            <li class="sidebar-item">
                                <a href="receive_product.php" class='sidebar-link'> <i class="bi bi-database-add"></i></i> <span>Receive the product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                            </li>

                            <li class="sidebar-item  has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i class="bi bi-stack"></i>
                                    <span>Product Detail</span>
                                </a>
                                <ul class="submenu">
                                    <li class="submenu-item">
                                        <a href="product_status.php"><span>Product Status <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>

                                    <li class="submenu-item">
                                        <a href="product_type.php"><span>Product Type <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Add other menu items based on user permissions -->
                            <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i class="bi bi-clock-history"></i>
                                    <span>History</span>
                                </a>
                                <ul class="submenu active">
                                    <li class="submenu-item">
                                        <a href="History_withdraw.php"><span>Withdraw <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>

                                    <li class="submenu-item active">
                                        <a href="History_receive.php"><span>Receive <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-title">Account Setting</li>

                            <li class="sidebar-item">
                                <a href="account_set.php" class='sidebar-link'> <i class="bi bi-people-fill"></i></i> <span> Users</a>
                            </li>

                            <li class="sidebar-item">
                                <a href="department.php" class='sidebar-link'> <i class="bi bi-person-vcard"></i></i> <span> department</a>
                            </li>

                        </ul>
                    </div>
                    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
                </div>
            </div>
            <!-- End Sidebar -->

            <?php
            include_once 'navbar.php';
            ?>


            <div id="main">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3 class="mb-2">History Receive</h3>
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
                                            <th style="text-align: center;">Receive ID</th>
                                            <th style="text-align: center;">User Name</th>
                                            <th style="text-align: center;">Date</th>
                                            <th style="text-align: center;">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result_receive_history->fetch_assoc()) : ?>
                                            <tr id="row_<?php echo $row['rec_id']; ?>" class="order-row" data-order-id="<?php echo $row['rec_id']; ?>">
                                                <td align="center"><?php echo $row['rec_id']; ?></td>
                                                <td align="center"><?php echo $row['us_name']; ?></td>
                                                <td align="center"><?php echo $row['rec_date']; ?></td>
                                                <td align="center"><?php echo $row['rec_time']; ?></td>
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
                            url: '/Final_Project/api/api_receive_details.php', // PHP file that handles fetching order details
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

                //นับจำนวนสินค้าที่อยู่ในรถเข็นขึ้น show ที่ปุ่ม
                $(document).ready(function() {
                    // ดึงค่า withdraw_count จาก Local Storage ถ้ามี
                    let savedWithdrawCount = localStorage.getItem('withdraw_count');
                    if (savedWithdrawCount !== null) {
                        $('#withdraw_count').text(savedWithdrawCount);
                    }

                    // Fetch the cart count on page load
                    updateCartCount();

                    function updateCartCount() {
                        $.ajax({
                            url: '/Final_Project/api/api_withdraw_count.php', // Adjust the path as needed
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                console.log(response); // ตรวจสอบว่าข้อมูลมาถูกต้อง
                                if (response.status === "success") {
                                    console.log("Updating withdraw_count to:", response.total_items);
                                    $('#withdraw_count').text(response.total_items);

                                    // เก็บค่าใน Local Storage
                                    localStorage.setItem('withdraw_count', response.total_items);
                                } else {
                                    console.log("API status is not 'success'");
                                    $('#withdraw_count').text(0); // Fallback if something goes wrong
                                    localStorage.setItem('withdraw_count', 0);
                                }
                            },
                            error: function() {
                                $('#withdraw_count').text(0); // Fallback in case of error
                                localStorage.setItem('withdraw_count', 0);
                            }
                        });
                    }
                });
            </script>

        <?php
    } else {
        header("location: error_user_page.php");
    }
        ?>