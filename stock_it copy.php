<?php

session_start();
include_once 'config/connect_db.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 3)) {

    $stock = '1';
    
//ดึงข้อมูลออกมาแสดงตาราง
    $sql = "SELECT 
            product.prod_id, 
            product.prod_name, 
            product.prod_amount, 
            product.prod_amount_min, 
            product.prod_price, 
            product.prod_unit, 
            product_status.prod_status_desc, 
            product_type.prod_type_desc 
        FROM product
        LEFT JOIN product_status ON product.prod_status = product_status.prod_status 
        LEFT JOIN product_type ON product.prod_type_id = product_type.prod_type_id
        WHERE st_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stock);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();
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

        <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
        <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
        <link rel="stylesheet" href="assets/css/app.css">
        <link rel="shortcut icon" href="assets/images/logo/optinova.jpg" type="image/x-icon">


    </head>

    <?php
    // Check the user level and include relevant files
    if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "Admin") {
        include_once 'menu_admin.php';
    } else {
        include_once 'menu_user.php';
    }
    include_once 'navbar.php';
    ?>


    <div id="main">

        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Inventory</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        Simple Datatable
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">ID</th>
                                    <th style="text-align: center;">Product Name</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">Price</th>
                                    <th style="text-align: center;">Unit</th>
                                    <th style="text-align: center;">Type</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['prod_id']; ?>">
                                        <td align="center"><?php echo $row['prod_id']; ?></td>
                                        <td align="center"><?php echo $row['prod_name']; ?></td>
                                        <td align="right" style="color: <?php echo ($row['prod_amount'] <= $row['prod_amount_min']) ? 'red' : ''; ?>;">
                                            <?php echo $row['prod_amount']; ?>
                                        </td>
                                        <td align="right"><?php echo $row['prod_price']; ?></td>
                                        <td align="center"><?php echo $row['prod_unit']; ?></td>
                                        <td align="center"><?php echo $row['prod_type_desc']; ?></td>
                                        <td align="center">
                                            <?php
                                            // Determine the badge class based on the status
                                            $badge_class = ($row['prod_status_desc'] === 'Active') ? 'badge bg-success' : 'badge bg-danger';
                                            ?>
                                            <span class="<?php echo $badge_class; ?>"><?php echo $row['prod_status_desc']; ?></span>
                                        </td>
                                        <td align="center">
                                            <a href="add_cart.php?prod_id=<?php echo $row['prod_id']; ?>" class="btn btn-primary rounded-pill">เบิก</a>

                                            <button type="button" class="btn btn-warning rounded-pill" data-bs-toggle="modal" data-bs-backdrop="false" data-bs-target="#editForm">
                                                แก้ไข
                                            </button>

                                            <button class="btn btn-danger rounded-pill" onclick="deleteProduct(<?php echo $row['prod_id']; ?>)">ลบ</button>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>


        <!--login form Modal -->
        <div class="modal fade text-left" id="editForm" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Form</h4>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body">
                            <label>ID: </label>
                            <div class="form-group">
                                <input type="text" placeholder="ID / รหัสสินค้า"
                                    class="form-control">
                            </div>
                            <label>Product Name: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Product Name / ชื่อสินค้า"
                                    class="form-control">
                            </div>
                            <label>Amount: </label>
                            <div class="form-group">
                                <input type="number" placeholder="Amount / จำนวนสินค้า"
                                    class="form-control">
                            </div>
                            <label>Amount Min: </label>
                            <div class="form-group">
                                <input type="number" placeholder="Amount Min / จำนวนสินค้าขั้นต่ำ"
                                    class="form-control">
                            </div>
                            <label>Unit: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Unit / หน่วยสินค้า"
                                    class="form-control">
                            </div>
                            <label>Type: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Type / ประเภทสินค้า"
                                    class="form-control">
                            </div>
                            <label>Status: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Status / สถานะสินค้า"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary"
                                data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="button" class="btn btn-primary ml-1"
                                data-bs-dismiss="modal">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Confirm</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--login form Modal End-->


        <script>
            function deleteProduct(prod_id) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/Final_Project/api/api_delete_pd.php",
                            type: 'POST',
                            dataType: "json",
                            data: {
                                prod_id: prod_id
                            },
                            success: function(result) {
                                if (result.color === "success") {
                                    // Remove the row from the table
                                    $('#row_' + prod_id).remove();
                                    // Show success message and reload the page
                                    Swal.fire({
                                        title: "Deleted!",
                                        icon: result.color,
                                        text: result.status
                                    });
                                } else {
                                    // Show error message
                                    Swal.fire({
                                        title: "Error!",
                                        icon: result.color,
                                        text: result.status
                                    });
                                }
                            }
                        });
                    }
                });
            }
        </script>









        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>Develop by</p>
                </div>
                <div class="float-end">
                <p><span class="text-danger"><i class="bi bi-heart"></i>    </span>Chakris Pantuwech <span class="text-danger"><i class="bi bi-heart"></i></p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/fontawesome/all.min.js"></script>
    <script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script>
        // Simple Datatable
        let table1 = document.querySelector('#table1');
        let dataTable = new simpleDatatables.DataTable(table1);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
    <script src="assets/js/main.js"></script>
    </body>

    </html>

<?php
} else {
    header("location: error_user_page.php");
}
?>