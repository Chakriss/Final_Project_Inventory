<?php
session_start();

include_once '../config/function.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    $result_dept = selectDept($conn);

    $user_stock = $_SESSION["user_stock"];


    if ($user_stock == 1) {
        $stock = 'stock_it.php';
    } else {
        $stock = 'stock_hr.php';
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
        <link rel="stylesheet" href="../assets/css/bootstrap.css">
        <link rel="stylesheet" href="../assets/vendors/fontawesome/all.min.css">

        <link rel="stylesheet" href="../assets/vendors/iconly/bold.css">
        <link rel="stylesheet" href="../assets/vendors/simple-datatables/style.css">
        <link rel="stylesheet" href="../assets/vendors/choices.js/choices.min.css" />
        <link rel="stylesheet" href="../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../assets/css/app.css">
        <link rel="shortcut icon" href="../assets/images/logo/optinova.jpg" type="image/x-icon">

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
                                <a href="admin_page.php"><img src="../assets/images/logo/logo_optinova.png" alt="Logo" style="width: 200px; height: auto;" srcset=""></a>
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
                                <a href="<?php echo $stock ?>" class='sidebar-link'> <i class="bi bi-database"></i> <span>Product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
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

                            <li class="sidebar-item">
                                <a href="withdraw.php" class='sidebar-link'> <i class="bi bi-cart-check-fill"></i></i> <span>Withdraw</span> <span id="withdraw_count"></span></a>
                            </li>

                            <!-- Add other menu items based on user permissions -->
                            <li class="sidebar-item  has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i class="bi bi-clock-history"></i>
                                    <span>History</span>
                                </a>
                                <ul class="submenu">
                                    <li class="submenu-item">
                                        <a href="History_withdraw.php"><span>Withdraw <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>

                                    <li class="submenu-item">
                                        <a href="History_receive.php"><span>Receive <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i class="bi bi-people-fill"></i>
                                    <span>Account</span>
                                </a>
                                <ul class="submenu active">
                                    <li class="submenu-item">
                                        <a href="account_set.php"><span> Users</a>
                                    </li>

                                    <li class="submenu-item active">
                                        <a href="department.php"><span> department</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clipboard2-fill"></i>
                                <span>Report</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="report_product_min.php"><span> Products Low</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_popular_product.php"><span> Popular Products</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_withdraw_most.php"><span> Department Withdraws Most Products</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_totalprice_most.php"><span> Department Total Price Most Products</a>
                                </li>
                            </ul>
                        </li>

                        </ul>
                    </div>
                    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
                </div>
            </div>
            <!-- End Sidebar -->

            <?php
            include_once '../navbar.php';
            ?>


            <div id="main">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Department</h3>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                <!-- Button trigger for Add Product form modal -->
                                <button type="button" class="btn btn-primary" data-bs-backdrop="false" data-bs-toggle="modal"
                                    data-bs-target="#modalAddDepartment">
                                    + New Department
                                </button>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover" id="table1">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">Department ID</th>
                                            <th style="text-align: center;">Department Name</th>
                                            <th style="text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result_dept->fetch_assoc()) : ?>
                                            <tr id="row_<?php echo $row['dept_id']; ?>">
                                                <td align="center"><?php echo $row['dept_id']; ?></td>
                                                <td align="center"><?php echo $row['dept_name']; ?></td>
                                                <td align="center">
                                                    <a href="javascript:void(0)" class="btn btn-warning"
                                                        onclick="showEditModal('<?php echo $row['dept_id']; ?>', '<?php echo $row['dept_name']; ?>')">
                                                        <span class="fas fa-edit"></span> Edit
                                                    </a>

                                                    <button class="btn btn-danger" onclick="deleteDept(<?php echo $row['dept_id']; ?>)">
                                                        <span class="fas fa-trash-alt"></span> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>


                <!-- Add Department form Modal l -->
                <div class="modal fade text-left" id="modalAddDepartment" tabindex="-1"
                    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                        role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h4 class="modal-title white" id="myModalLabel33">New Department</h4>
                                <button type="button" class="close" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Department Name: </label>
                                            <div class="form-group">
                                                <input type="text" id="dept_name" placeholder="Enter Department Name"
                                                    class="form-control">
                                                <div class="invalid-feedback" id="deptNameFeedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Cancle</span>
                                        </button>
                                        <button type="button" class="btn btn-success ml-1" onclick="addDept()">
                                            <i class="bx bx-check d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Add</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Add Department form Modal -->

                <!-- Edit Department  Modal -->
                <div class="modal fade text-left" id="modalEditDepartment" tabindex="-1"
                    role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                        role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h4 class="modal-title white" id="editModalLabel">Edit Product Type</h4>
                                <button type="button" class="close" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" id="edit_department_id">
                                            <label>Department Name: </label>
                                            <div class="form-group">
                                                <input type="text" id="edit_dept_name" placeholder="Enter Department Name"
                                                    class="form-control">
                                                <div class="invalid-feedback" id="editDeptFeedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <span>Cancel</span>
                                        </button>
                                        <button type="button" class="btn btn-success ml-1" onclick="updateDept()">
                                            <span>Update</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Edit Department Modal -->

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

            <script src="../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
            <script src="../assets/js/bootstrap.bundle.min.js"></script>
            <script src="../assets/vendors/fontawesome/all.min.js"></script>
            <script src="../assets/vendors/simple-datatables/simple-datatables.js"></script>
            <script src="../assets/vendors/choices.js/choices.min.js"></script>
            <script>
                // Simple Datatable
                let table1 = document.querySelector('#table1');
                let dataTable = new simpleDatatables.DataTable(table1);
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
            <script src="../assets/js/main.js"></script>

            <script>
                //ฟังชันเพิ่มประเภทสินค้า
                function addDept() {
                    event.preventDefault();
                    let isValid = true;

                    // Reset validation messages
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');

                    // Form validation checks
                    if ($('#dept_name').val() == "") {
                        $('#dept_name').addClass('is-invalid');
                        $('#deptNameFeedback').text("Department Name is empty.");
                        isValid = false;
                    }
                    if (isValid) {
                        let formData = new FormData();
                        formData.append('dept', $('#dept_name').val());

                        $.ajax({
                            url: "/Final_Project/api/api_add_dept.php",
                            type: 'POST',
                            dataType: "json",
                            data: formData,
                            processData: false, // Prevent jQuery from converting the FormData object into a query string
                            contentType: false, // Prevent jQuery from overriding the content type
                            success: function(result) {
                                if (result.status === "successfully") {
                                    Swal.fire({
                                        title: 'Add department success!',
                                        icon: 'success',
                                        timer: 1000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Wrong department added!",
                                        text: result.message,
                                        icon: "error"
                                    });
                                }
                            }
                        });
                    } else {
                        // Validation failed, show an error message and keep the modal open
                        Swal.fire({
                            title: "Wrong department added!",
                            text: "Please fill in all information completely.",
                            icon: "error"
                        });
                        return; // Keep modal open if validation fails
                    }
                }

                //ฟังชันลบประเภทสินค้า
                function deleteDept(dept_id) {
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
                                url: "/Final_Project/api/api_delete_dept.php",
                                type: 'POST',
                                dataType: "json",
                                data: {
                                    dept_id: dept_id
                                },
                                success: function(result) {
                                    if (result.color === "success") {
                                        // Remove the row from the table
                                        $('#row_' + dept_id).remove();
                                        // Show success message and reload the page
                                        Swal.fire({
                                            title: "Deleted!",
                                            icon: result.color,
                                            text: result.status,
                                            timer: 1000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        // Show error message
                                        Swal.fire({
                                            title: "Error!",
                                            icon: result.status,
                                            text: result.message
                                        });
                                    }
                                }
                            });
                        }
                    });
                }

                function showEditModal(deptId, dept_Name) {
                    // Populate the modal with the product type data
                    $('#edit_department_id').val(deptId);
                    $('#edit_dept_name').val(dept_Name);

                    // Open the modal
                    $('#modalEditDepartment').modal('show');
                }

                function updateDept() {
                    event.preventDefault();
                    let isValid = true;

                    // Reset validation messages
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');

                    if ($('#edit_dept_name').val() === "") {
                        $('#edit_dept_name').addClass('is-invalid');
                        $('#editDeptFeedback').text("Department Name is empty.");
                        isValid = false;
                    }

                    if (isValid) {
                        let formData = new FormData();
                        formData.append('id', $('#edit_department_id').val());
                        formData.append('name', $('#edit_dept_name').val());

                        $.ajax({
                            url: "/Final_Project/api/api_update_dept.php",
                            type: 'POST',
                            dataType: "json",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(result) {
                                if (result.status === "successfully") {
                                    Swal.fire({
                                        title: 'Edited successfully!',
                                        icon: 'success',
                                        timer: 1000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Edit Error!",
                                        text: result.message,
                                        icon: "error"
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: "Edit Error!",
                                    text: "An error occurred: " + error,
                                    icon: "error"
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Edit Error!",
                            text: "Please check if any information is incorrect.",
                            icon: "error"
                        });
                    }
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
    header("location: ../admin/error_admin_page.php");
}
?>