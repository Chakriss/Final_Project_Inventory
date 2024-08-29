<?php
session_start();
include_once 'config/connect_db.php';
include_once 'config/function.php';

include_once 'header.php';
include_once 'menu_admin.php';
include_once 'navbar.php';

//เช็คว่ารับค่า prod_type_id ก่อนเข้าหน้านี้
if (empty($_GET["prod_type_id"])) {
    echo "<script type='text/javascript'>";
    echo "alert('An error occurred. Please select the Type first!!');";
    echo "window.location = 'product_type.php'; ";
    echo "</script>";
}

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    $prod_type_id = $_GET["prod_type_id"];

    $result = editType($conn, $prod_type_id);
    $type = $result->fetch_assoc();

?>

    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Edit Type</h3>
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
                                    <label for="basicInput">Type ID:</label>
                                    <input type="text" class="form-control" id="prod_type_id" value="<?php echo $type['prod_type_id']; ?>" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">Type Name:</label>
                                    <input type="text" class="form-control" id="prod_type_desc" value="<?php echo $type['prod_type_desc']; ?>" placeholder="Please enter Type name / กรุณากรอกชื่อประเภทสินค้า">
                                    <div class="invalid-feedback" id="nameTypeFeedback"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <button type="button" class="btn btn-secondary btn-fw" onclick="window.location.href='product_type.php';">Cancle</button>
                <button type="submit" class="btn btn-success btn-fw" onclick="update_type(event)">Update</button>

            </section>
        </div>

        <?php
        include_once 'footer.php';
        ?>

        <script>
            function update_type(event) {
                event.preventDefault();
                let isValid = true;

                // Reset validation messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                if ($('#prod_type_desc').val() === "") {
                    $('#prod_type_desc').addClass('is-invalid');
                    $('#nameTypeFeedback').text("Type Name is empty.");
                    isValid = false;
                }

                if (isValid) {
                    let formData = new FormData();
                    formData.append('id', $('#prod_type_id').val());
                    formData.append('name', $('#prod_type_desc').val());

                    $.ajax({
                        url: "/Final_Project/api/api_update_type.php",
                        type: 'POST',
                        dataType: "json",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.status === "successfully") {
                                Swal.fire({
                                    title: 'Edited successfully!',
                                    icon: 'success'
                                }).then((result) => {
                                    window.location.href = 'product_type.php';
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
        </script>


    <?php
} else {
    header("location: error_user_page.php");
}
    ?>