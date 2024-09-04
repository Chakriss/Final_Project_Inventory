<?php
session_start();

include_once 'config/function.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    $result_admin = selectAdmin($conn);
    $result_user = selectUser($conn);

    //เรียกใช้ฟังชันดึงแผนก
    $result_dept = selectDept($conn);
    $result_stock = selectStock($conn);
    $result_status_user = selectStatusUser($conn);

    $depts = [];
    while ($dept = $result_dept->fetch_assoc()) {
        $depts[] = $dept;
    }

    $statuses = [];
    while ($status = $result_status_user->fetch_assoc()) {
        $statuses[] = $status;
    }
?>
    <script>
        // Pass PHP data to JavaScript
        const depts = <?php echo json_encode($depts); ?>;
        const statuses = <?php echo json_encode($statuses); ?>;
    </script>

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
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Admin</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <!-- Button trigger for Add Admin form Modal -->
                        <button type="button" class="btn btn-primary" data-bs-backdrop="false" data-bs-toggle="modal"
                            data-bs-target="#modalAddAdmin">
                            + New Admin
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Admin Name</th>
                                    <th style="text-align: center;">Admin Email</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_admin->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['us_id']; ?>">
                                        <td align="center"><?php echo $row['us_name']; ?></td>
                                        <td align="center"><?php echo $row['us_email']; ?></td>
                                        <td align="center">
                                            <?php
                                            $badge_class = ($row['us_status_desc'] === 'Active') ? 'badge bg-success' : 'badge bg-danger';
                                            ?>
                                            <span class="<?php echo $badge_class; ?>"><?php echo $row['us_status_desc']; ?></span>
                                        </td>
                                        <td align="center">
                                            <a href="edit_admin.php?us_id=<?php echo $row['us_id']; ?>" class="btn btn-warning">
                                                <span class="fas fa-edit"></span> Edit
                                            </a>

                                            <button class="btn btn-danger" onclick="deleteUser(<?php echo $row['us_id']; ?>)">
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

            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Users</h3>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <!-- Button trigger for Add User form Modal -->
                        <button type="button" class="btn btn-primary" data-bs-backdrop="false" data-bs-toggle="modal"
                            data-bs-target="#modalAddUser">
                            + New User
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table2">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">User Name</th>
                                    <th style="text-align: center;">User Email</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_user->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['us_id']; ?>">
                                        <td align="center"><?php echo $row['us_name']; ?></td>
                                        <td align="center"><?php echo $row['us_email']; ?></td>
                                        <td align="center">
                                            <?php
                                            $badge_class = ($row['us_status_desc'] === 'Active') ? 'badge bg-success' : 'badge bg-danger';
                                            ?>
                                            <span class="<?php echo $badge_class; ?>"><?php echo $row['us_status_desc']; ?></span>
                                        </td>
                                        <td align="center">
                                            <a href="edit_user.php?us_id=<?php echo $row['us_id']; ?>" class="btn btn-warning">
                                                <span class="fas fa-edit"></span> Edit
                                            </a>

                                            <button class="btn btn-danger" onclick="deleteUser(<?php echo $row['us_id']; ?>)">
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


        <!-- Add Admin form Modal  -->
        <div class="modal fade text-left" id="modalAddAdmin" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">New Admin</h4>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Name: </label>
                                    <div class="form-group">
                                        <input type="text" id="us_name_admin" placeholder="Enter your Name"
                                            class="form-control">
                                        <div class="invalid-feedback" id="nameAdminFeedback"></div>
                                    </div>
                                    <label>Email: </label>
                                    <div class="form-group">
                                        <input type="email" id="us_email_admin" placeholder="Enter your Email"
                                            class="form-control">
                                        <div class="invalid-feedback" id="emailAdminFeedback"></div>
                                    </div>
                                    <label>Password: </label>
                                    <div class="form-group">
                                        <input type="password" id="us_password_admin" placeholder="Enter your Password"
                                            class="form-control">
                                        <div class="invalid-feedback" id="passwordAdminFeedback"></div>
                                    </div>
                                    <label>Confirm Password: </label>
                                    <div class="form-group">
                                        <input type="password" id="us_confirmpassword_admin" placeholder="Enter your Confirm Password"
                                            class="form-control">
                                        <div class="invalid-feedback" id="confirmpasswordAdminFeedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Department:</label>
                                    <div class="form-group">
                                        <select class="form-select" id="us_dept_admin">
                                            <option value="" selected>Select Department</option>
                                            <?php foreach ($depts as $dept) : ?>
                                                <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="invalid-feedback" id="deptAdminFeedback"></div>
                                    </div>

                                    <label>Stock:</label>
                                    <div class="form-group">
                                        <select class="form-select" id="stock">
                                            <option value="" selected>Select Stock</option>
                                            <?php while ($stock = $result_stock->fetch_assoc()) : ?>
                                                <option value="<?php echo $stock['st_id']; ?>"><?php echo $stock['st_name']; ?></option>
                                            <?php endwhile ?>
                                        </select>
                                        <div class="invalid-feedback" id="stockAdminFeedback"></div>
                                    </div>
                                    <label>Status: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="us_status">
                                            <option value="" selected>Select Status</option> <!-- Default option -->
                                            <?php foreach ($statuses as $status) : ?>
                                                <option value="<?php echo $status['us_status_id']; ?>"><?php echo $status['us_status_desc']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="invalid-feedback" id="statusAdminFeedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Cancle</span>
                                </button>
                                <button type="button" class="btn btn-success ml-1" onclick="addAdmin()">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Add</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Admin form Modal -->

        <!-- Add User form Modal  -->
        <div class="modal fade text-left" id="modalAddUser" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">New User</h4>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Name: </label>
                                    <div class="form-group">
                                        <input type="text" id="us_name_user" placeholder="Enter your Name"
                                            class="form-control">
                                        <div class="invalid-feedback" id="nameUserFeedback"></div>
                                    </div>
                                    <label>Email: </label>
                                    <div class="form-group">
                                        <input type="email" id="us_email_user" placeholder="Enter your Email"
                                            class="form-control">
                                        <div class="invalid-feedback" id="emailUserFeedback"></div>
                                    </div>
                                    <label>Password: </label>
                                    <div class="form-group">
                                        <input type="password" id="us_password_user" placeholder="Enter your Password"
                                            class="form-control">
                                        <div class="invalid-feedback" id="passwordUserFeedback"></div>
                                    </div>
                                    <label>Confirm Password: </label>
                                    <div class="form-group">
                                        <input type="password" id="us_confirmpassword_user" placeholder="Enter your Confirm Password"
                                            class="form-control">
                                        <div class="invalid-feedback" id="confirmpasswordUserFeedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Department:</label>
                                    <div class="form-group">
                                        <select class="form-select" id="us_dept_user">
                                            <option value="" selected>Select Department</option>
                                            <?php foreach ($depts as $dept) : ?>
                                                <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="invalid-feedback" id="deptUserFeedback"></div>
                                    </div>

                                    <label>Status: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="us_status_user">
                                            <option value="" selected>Select Status</option> <!-- Default option -->
                                            <?php foreach ($statuses as $status) : ?>
                                                <option value="<?php echo $status['us_status_id']; ?>"><?php echo $status['us_status_desc']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="invalid-feedback" id="statusUserFeedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Cancle</span>
                                </button>
                                <button type="button" class="btn btn-success ml-1" onclick="addUser()">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Add</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add User form Modal -->

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

        // Simple Datatable
        let table2 = document.querySelector('#table2');
        let dataTable2 = new simpleDatatables.DataTable(table2);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
    <script src="assets/js/main.js"></script>

    <script>
        //ฟังชันเพิ่มผู้ดูแล
        function addAdmin() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Form validation checks
            if ($('#us_name_admin').val() == "") {
                $('#us_name_admin').addClass('is-invalid');
                $('#nameAdminFeedback').text("Name is empty.");
                isValid = false;
            }
            if ($('#us_email_admin').val() == "") {
                $('#us_email_admin').addClass('is-invalid');
                $('#emailAdminFeedback').text("Email is empty.");
                isValid = false;
            }
            if ($('#us_password_admin').val() == "") {
                $('#us_password_admin').addClass('is-invalid');
                $('#passwordAdminFeedback').text("Password is empty.");
                isValid = false;
            }
            if ($('#us_confirmpassword_admin').val() == "") {
                $('#us_confirmpassword_admin').addClass('is-invalid');
                $('#confirmpasswordAdminFeedback').text("Confirm Password is empty.");
                isValid = false;
            }
            if ($('#us_dept_admin').val() == "") {
                $('#us_dept_admin').addClass('is-invalid');
                $('#deptAdminFeedback').text("Department is empty.");
                isValid = false;
            }
            if ($('#stock').val() == "") {
                $('#stock').addClass('is-invalid');
                $('#stockAdminFeedback').text("Stock is empty.");
                isValid = false;
            }
            if ($('#us_status').val() == "") {
                $('#us_status').addClass('is-invalid');
                $('#statusAdminFeedback').text("Status is empty.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('name', $('#us_name_admin').val());
                formData.append('email', $('#us_email_admin').val());
                formData.append('password', $('#us_password_admin').val());
                formData.append('confirmpassword', $('#us_confirmpassword_admin').val());
                formData.append('dept', $('#us_dept_admin').val());
                formData.append('stock', $('#stock').val());
                formData.append('status', $('#us_status').val());

                $.ajax({
                    url: "/Final_Project/api/api_add_admin.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false, // Prevent jQuery from converting the FormData object into a query string
                    contentType: false, // Prevent jQuery from overriding the content type
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Add Admin success!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Wrong Admin added!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                // Validation failed, show an error message and keep the modal open
                Swal.fire({
                    title: "Wrong Admin added!",
                    text: "Please fill in all information completely.",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
            }
        }

        //ฟังชันเพิ่มผู้ใช้
        function addUser() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Form validation checks
            if ($('#us_name_user').val() == "") {
                $('#us_name_user').addClass('is-invalid');
                $('#nameUserFeedback').text("Name is empty.");
                isValid = false;
            }
            if ($('#us_email_user').val() == "") {
                $('#us_email_user').addClass('is-invalid');
                $('#emailAdminFeedback').text("Email is empty.");
                isValid = false;
            }
            if ($('#us_password_user').val() == "") {
                $('#us_password_user').addClass('is-invalid');
                $('#passwordUserFeedback').text("Password is empty.");
                isValid = false;
            }
            if ($('#us_confirmpassword_user').val() == "") {
                $('#us_confirmpassword_user').addClass('is-invalid');
                $('#confirmpasswordUserFeedback').text("Confirm Password is empty.");
                isValid = false;
            }
            if ($('#us_dept_user').val() == "") {
                $('#us_dept_user').addClass('is-invalid');
                $('#deptUserFeedback').text("Department is empty.");
                isValid = false;
            }
            if ($('#us_status_user').val() == "") {
                $('#us_status_user').addClass('is-invalid');
                $('#statusUserFeedback').text("Status is empty.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('name', $('#us_name_user').val());
                formData.append('email', $('#us_email_user').val());
                formData.append('password', $('#us_password_user').val());
                formData.append('confirmpassword', $('#us_confirmpassword_user').val());
                formData.append('dept', $('#us_dept_user').val());
                formData.append('status', $('#us_status_user').val());

                $.ajax({
                    url: "/Final_Project/api/api_add_user.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false, // Prevent jQuery from converting the FormData object into a query string
                    contentType: false, // Prevent jQuery from overriding the content type
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Add User success!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Wrong User added!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                // Validation failed, show an error message and keep the modal open
                Swal.fire({
                    title: "Wrong User added!",
                    text: "Please fill in all information completely.",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
            }
        }

        //ฟังชันลบประเภทสินค้า
        function deleteUser(us_id) {
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
                        url: "/Final_Project/api/api_delete_user.php",
                        type: 'POST',
                        dataType: "json",
                        data: {
                            us_id: us_id
                        },
                        success: function(result) {
                            if (result.color === "success") {
                                // Remove the row from the table
                                $('#row_' + us_id).remove();
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
    </script>

    </body>

    </html>

<?php
} else {
    header("location: error_user_page.php");
}
?>