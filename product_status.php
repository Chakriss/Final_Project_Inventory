<?php
session_start();

include_once 'config/function.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {
    $stock = $_SESSION['user_stock'];
    // Fetch product data using the selectProduct function
    $result = selectProduct($conn, $stock);
    if ($result === false) {
        echo "Failed to retrieve product data.";
        exit();
    }

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
                                <a href="#" class='sidebar-link'> <i class="bi bi-cart-check-fill"></i> </i> <span>Withdraw</span> <span id="withdraw_count"></span></a>
                            </li>

                            <li class="sidebar-item">
                                <a href="receive_product.php" class='sidebar-link'> <i class="bi bi-database-add"></i></i> <span>Receive the product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                            </li>

                            <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i class="bi bi-stack"></i>
                                    <span>Product Detail</span>
                                </a>
                                <ul class="submenu active">
                                    <li class="submenu-item active">
                                        <a href="product_status.php"><span>Product Status <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>

                                    <li class="submenu-item">
                                        <a href="product_type.php"><span>Product Type <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Add other menu items based on user permissions -->
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
            // include_once 'menu_admin.php';
            include_once 'navbar.php';
            ?>



            <div id="main">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Product Status</h3>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                <div class="bulk-actions">
                                    <button type="button" class="btn btn-primary" id="bulk-update-status">Update All</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover" id="table1">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">
                                                <input type="checkbox" id="select-all" class="form-check-input"> <!-- Checkbox to select all -->

                                            </th>
                                            <th style="text-align: center;">Product ID</th>
                                            <th style="text-align: center;"> Photo </th>
                                            <th style="text-align: center;">Name</th>
                                            <th style="text-align: center;">Type</th>
                                            <th style="text-align: center;">Status</th>
                                            <th style="text-align: center;">Off-On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()) : ?>
                                            <tr id="row_<?php echo $row['prod_id']; ?>">
                                                <td align="center">
                                                    <input type="checkbox" class="product-checkbox" data-id="<?php echo $row['prod_id']; ?>">
                                                </td>
                                                <td align="center"><?php echo $row['prod_id']; ?></td>
                                                <td align="center">
                                                    <img src="photo/<?php echo $row['prod_img']; ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10%;" onclick="expandImage('<?php echo $row['prod_img']; ?>')">
                                                </td>
                                                <td align="center"><?php echo $row['prod_name']; ?></td>
                                                <td align="center"><?php echo $row['prod_type_desc']; ?></td>
                                                <td align="center">
                                                    <?php
                                                    $badge_class = ($row['prod_status_desc'] === 'Active') ? 'badge bg-success' : 'badge bg-danger';
                                                    ?>
                                                    <span class="<?php echo $badge_class; ?>"><?php echo $row['prod_status_desc']; ?></span>
                                                </td>
                                                <td align="center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input switch-status" type="checkbox" id="flexSwitchCheckChecked" data-id="<?php echo $row['prod_id']; ?>" <?php echo ($row['prod_status_desc'] === 'Active') ? 'checked' : ''; ?>>
                                                        </div>
                                                    </div>
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
                $(document).ready(function() {
                    // Handle select all checkbox
                    $('#select-all').on('change', function() {
                        $('.product-checkbox').prop('checked', this.checked);
                    });

                    // Handle individual product checkbox change
                    $('.product-checkbox').on('change', function() {
                        if (!this.checked) {
                            $('#select-all').prop('checked', false);
                        }

                        // If all product checkboxes are checked, also check the "select all" checkbox
                        if ($('.product-checkbox:checked').length === $('.product-checkbox').length) {
                            $('#select-all').prop('checked', true);
                        }
                    });

                    //เปลี่ยนสถานะรายการเดียว
                    $('.switch-status').on('change', function() {
                        var prod_id = $(this).data('id');
                        var status = $(this).is(':checked') ? 'A' : 'I'; // 'A' for available, 'I' for unavailable

                        $.ajax({
                            url: '/Final_Project/api/api_update_product_status.php',
                            method: 'POST',
                            dataType: "json",
                            data: {
                                prod_id: prod_id,
                                prod_status: status
                            },
                            success: function(response) {
                                // var jsonResponse = JSON.parse(response);
                                if (response.status === 'successfully') {
                                    // Update the status in the UI based on the new status
                                    var row = $('#row_' + prod_id);
                                    var badge = row.find('td:eq(5) span');

                                    if (status === 'A') {
                                        badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                                    } else {
                                        badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'An error occurred.',
                                        text: response.message || 'Could not update the status.'
                                    });

                                    // Revert the switch status if there was an error
                                    $(this).prop('checked', !$(this).is(':checked'));
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'An error occurred.',
                                    text: 'Could not update the status.'
                                });

                                // Revert the switch status if there was an error
                                $(this).prop('checked', !$(this).is(':checked'));
                            }
                        });
                    });


                    //เปลี่ยนสถานะหลายรายการ
                    $('#bulk-update-status').on('click', function() {
                        var selectedProducts = [];

                        // Collect all selected product IDs
                        $('.product-checkbox:checked').each(function() {
                            selectedProducts.push($(this).data('id'));
                        });

                        if (selectedProducts.length > 0) {
                            Swal.fire({
                                title: 'Update Status',
                                text: "Select the new status for the selected products:",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Set to Active',
                                cancelButtonText: 'Set to Inactive',
                                showDenyButton: true,
                                denyButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    updateBulkStatus(selectedProducts, 'A'); // Set to 'A' (เบิกสินค้าได้)
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    updateBulkStatus(selectedProducts, 'I'); // Set to 'I' (ไม่สามารถเบิกได้)
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No products selected',
                                text: 'Please select at least one product to update.'
                            });
                        }
                    });

                    // Function to handle the bulk update AJAX request
                    function updateBulkStatus(productIds, status) {
                        $.ajax({
                            url: '/Final_Project/api/api_bulk_update_product_status.php',
                            method: 'POST',
                            data: {
                                prod_ids: productIds,
                                status: status
                            },
                            success: function(response) {
                                var jsonResponse = JSON.parse(response);
                                if (jsonResponse.status === 'successfully') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Status updated successfully!',
                                        timer: 1000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Update UI directly without page reload
                                        productIds.forEach(id => {
                                            var row = $('#row_' + id);
                                            var badge = row.find('td:eq(5) span');
                                            var switchStatus = row.find('.switch-status');

                                            if (status === 'A') {
                                                badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                                                switchStatus.prop('checked', true);
                                            } else {
                                                badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                                                switchStatus.prop('checked', false);
                                            }
                                        });
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: jsonResponse.message || 'Could not update the status.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Could not update the status.'
                                });
                            }
                        });
                    }
                });

                //ขยายรูปออกมา
                function expandImage(imageSrc) {
                    // Set the image source in the modal
                    $('#imageModalSrc').attr('src', 'photo/' + imageSrc);
                    // Show the modal
                    $('#imageModal').modal('show');
                }

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

    </body>

    </html>

<?php
} else {
    header("location: error_user_page.php");
}
?>