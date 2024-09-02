<?php
session_start();
include_once 'config/connect_db.php';
include_once 'config/function.php';

include_once 'header.php';
include_once 'menu_admin.php';
include_once 'navbar.php';


if (empty($_GET["us_id"])) {
    echo "<script type='text/javascript'>";
    echo "alert('An error occurred. Please select the product first!!');";
    echo "window.location = 'account_set.php'; ";
    echo "</script>";
}

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    $us_id = $_GET['us_id'];

    //เรียกใช้ฟังชันดึงข้อมูล
    $row = editAdmin($conn, $us_id);
    $result_dept = selectDept($conn);
    $result_stock = selectStock($conn);
    $result_status_user = selectStatusUser($conn);
    $result_permission = selectPermission($conn);

?>
    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Edit Product</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="basicInput">User ID:</label>
                                    <input type="text" class="form-control" id="us_id" value="<?php echo $row['us_id']; ?>" readonly="readonly">
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">Name:</label>
                                    <input type="text" class="form-control" id="us_name" value="<?php echo $row['us_name']; ?>" placeholder="Please enter name / กรุณากรอกชื่อผู้ใช้">
                                    <div class="invalid-feedback" id="nameFeedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">email:</label>
                                    <input type="email" class="form-control" id="us_email" value="<?php echo $row['us_email']; ?>" placeholder="Please enter email / กรุณากรอกอีเมล">
                                    <div class="invalid-feedback" id="emailFeedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">Password:</label>
                                    <input type="text" class="form-control" id="us_password" value="<?php echo $row['us_password']; ?>" placeholder="Please enter password/ กรุณากรอกระหัสผ่าน">
                                    <div class="invalid-feedback" id="passwordFeedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">Permission:</label>
                                    <select class="form-select" id="us_level">
                                        <?php
                                        $selected_level = $row['us_level_id'];
                                        while ($permission = $result_permission->fetch_assoc()) :
                                            $select_lv = ($permission['us_level_id'] == $selected_level) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $permission['us_level_id']; ?>" <?php echo $select_lv; ?>><?php echo $permission['us_level_desc']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="permissionFeedback"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="basicInput">Department:</label>
                                    <select class="choices form-select" id="dept_id">
                                        <?php
                                        $selected_dept = $row['dept_id'];
                                        while ($dept = $result_dept->fetch_assoc()) :
                                            $select_dept = ($dept['dept_id'] == $selected_dept) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $dept['dept_id']; ?>" <?php echo $select_dept; ?>><?php echo $dept['dept_name']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="deptFeedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">Stock:</label>
                                    <select class="form-select" id="st_id">
                                        <?php
                                        $selected_stock = $row['st_id'];
                                        while ($stock = $result_stock->fetch_assoc()) :
                                            $select_stock = ($stock['st_id'] == $selected_stock) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $stock['st_id']; ?>" <?php echo $select_stock; ?>><?php echo $stock['st_name']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="stockFeedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="basicInput">Status:</label>
                                    <select class="form-select" id="us_status_id">
                                        <?php
                                        $selected_status = $row['us_status_id'];
                                        while ($status = $result_status_user->fetch_assoc()) :
                                            $select = ($status['us_status_id'] == $selected_status) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $status['us_status_id']; ?>" <?php echo $select; ?>><?php echo $status['us_status_desc']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="statusFeedback"></div>
                                </div>


                            </div>
                        </div>

                    </div>

                </div>

                <button type="button" class="btn btn-secondary btn-fw" onclick="window.location.href='account_set.php';">Cancle</button>
                <button type="submit" class="btn btn-success btn-fw" onclick="update_admin()">Update</button>

            </section>

        </div>


        <?php
        include_once 'footer.php';
        ?>

        <script>
            function update_admin() {
                event.preventDefault();
                let isValid = true;

                // Reset validation messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                if ($('#us_name').val() == "") {
                    $('#us_name').addClass('is-invalid');
                    $('#nameFeedback').text("Name is empty.");
                    isValid = false;
                }
                if ($('#us_email').val() == "") {
                    $('#us_email').addClass('is-invalid');
                    $('#emailFeedback').text("Email is empty.");
                    isValid = false;
                }
                if ($('#us_password').val() == "") {
                    $('#us_password').addClass('is-invalid');
                    $('#passwordFeedback').text("Password is empty.");
                    isValid = false;
                }
                if ($('#us_level').val() == "") {
                    $('#us_level').addClass('is-invalid');
                    $('#permissionFeedback').text("Permission is empty.");
                    isValid = false;
                }
                if ($('#dept_id').val() == "") {
                    $('#dept_id').addClass('is-invalid');
                    $('#deptFeedback').text("Department is empty.");
                    isValid = false;
                }
                if ($('#st_id').val() == "") {
                    $('#st_id').addClass('is-invalid');
                    $('#stockFeedback').text("Stock is empty.");
                    isValid = false;
                }
                if ($('#us_status_id').val() == "") {
                    $('#us_status_id').addClass('is-invalid');
                    $('#statusFeedback').text("Status is empty.");
                    isValid = false;
                }

                if (isValid) {
                    let formData = new FormData();
                    formData.append('id', $('#us_id').val());
                    formData.append('name', $('#us_name').val());
                    formData.append('email', $('#us_email').val());
                    formData.append('password', $('#us_password').val());
                    formData.append('permission', $('#us_level').val());
                    formData.append('dept', $('#dept_id').val());
                    formData.append('stock', $('#st_id').val());
                    formData.append('status', $('#us_status_id').val());

                    $.ajax({
                        url: "/Final_Project/api/api_update_admin.php",
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
                                }).then((result) => {
                                    window.location.href = 'account_set.php';
                                });
                            } else if (result.status === "login_required") {
                                Swal.fire({
                                    title: "Profile Updated",
                                    text: result.message,
                                    icon: "info"
                                }).then((result) => {
                                    window.location.href = 'logout.php';
                                });
                            } else {
                                Swal.fire({
                                    title: "Edit Error!",
                                    text: result.message,
                                    icon: "error"
                                });
                            }
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
        </script>


    <?php
} else {
    header("location: error_user_page.php");
}
    ?>