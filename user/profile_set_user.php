<?php
session_start();
include_once '../config/function.php';

include_once '../header.php';
include_once 'menu_user.php';
include_once '../navbar.php';

$us_id = $_SESSION["user_id"];

if (empty($us_id)) {
    echo "<script type='text/javascript'>";
    echo "alert('An error occurred. Please select the account first!!');";
    echo "window.location = 'login.php'; ";
    echo "</script>";
}

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}


//เรียกใช้ฟังชันดึงข้อมูล
$row = editAdmin($conn, $us_id);
$result_dept = selectDept($conn);

?>
<div id="main">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Account</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">

                </div>

                <div class="card-body">
                    <div class="row">
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
                            <button type="button" class="btn btn-warning btn-fw" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                Reset Password
                            </button>

                        </div>
                    </div>

                </div>

            </div>

            <button type="button" class="btn btn-secondary btn-fw" onclick="history.back();">Cancle</button>
            <button type="submit" class="btn btn-success btn-fw" onclick="update_account()">Update</button>

        </section>

    </div>

    <!-- Modal reset password-->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                            <div class="invalid-feedback" id="passwordFeedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                            <div class="invalid-feedback" id="confirmPasswordFeedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="resetPassword()">Reset Password</button>
                </div>
            </div>
        </div>
    </div>



    <?php
    include_once '../footer.php';
    ?>

    <script>
        function update_account() {
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
            if ($('#dept_id').val() == "") {
                $('#dept_id').addClass('is-invalid');
                $('#deptFeedback').text("Department is empty.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('id', $('#us_id').val());
                formData.append('name', $('#us_name').val());
                formData.append('email', $('#us_email').val());
                formData.append('dept', $('#dept_id').val());

                $.ajax({
                    url: "/Final_Project/api/api_update_profile.php",
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
                                window.location.href = '../logout.php';
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

        function resetPassword() {
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            let newPassword = $('#newPassword').val();
            let confirmPassword = $('#confirmPassword').val();

            if (newPassword === "") {
                $('#newPassword').addClass('is-invalid');
                $('#passwordFeedback').text("New password is required.");
                isValid = false;
            }
            if (confirmPassword === "") {
                $('#confirmPassword').addClass('is-invalid');
                $('#confirmPasswordFeedback').text("Confirm password is required.");
                isValid = false;
            } else if (newPassword !== confirmPassword) {
                $('#confirmPassword').addClass('is-invalid');
                $('#confirmPasswordFeedback').text("Passwords do not match.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('user_id', $('#us_id').val());
                formData.append('new_password', newPassword);

                $.ajax({
                    url: "/Final_Project/api/api_reset_password.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Password Reset successfully!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then((result) => {
                                $('#resetPasswordModal').modal('hide');
                            });
                        } else {
                            Swal.fire({
                                title: "Reset Error!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            }
        }

        //reset password
        function resetPassword() {
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            let newPassword = $('#newPassword').val();
            let confirmPassword = $('#confirmPassword').val();

            if (newPassword === "") {
                $('#newPassword').addClass('is-invalid');
                $('#passwordFeedback').text("New password is required.");
                isValid = false;
            }
            if (confirmPassword === "") {
                $('#confirmPassword').addClass('is-invalid');
                $('#confirmPasswordFeedback').text("Confirm password is required.");
                isValid = false;
            } else if (newPassword !== confirmPassword) {
                $('#confirmPassword').addClass('is-invalid');
                $('#confirmPasswordFeedback').text("Passwords do not match.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('user_id', $('#us_id').val());
                formData.append('new_password', newPassword);

                $.ajax({
                    url: "/Final_Project/api/api_reset_password.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Password Reset successfully!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then((result) => {
                                $('#resetPasswordModal').modal('hide');
                                location.reload(); // รีเฟรชหน้าเว็บหลังจากปิด modal
                            });
                        } else {
                            Swal.fire({
                                title: "Reset Error!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            }
        }
    </script>