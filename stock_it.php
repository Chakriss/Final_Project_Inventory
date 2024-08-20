<?php

session_start();
include_once 'config/connect_db.php';
include_once 'config/function.php';


// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 3)) {
    $stock = 1;
    // Fetch product data using the selectProduct function
    $result = selectProduct($conn, $stock);
    if ($result === false) {
        echo "Failed to retrieve product data.";
        exit();
    }

    //เรียกใช้ฟังชันดึงประเภทสินค้า
    $result_type = selectType($conn);

    //เรียกใช้ฟังชันดึงสถานะสินค้า
    $result_status = selectStatus($conn);


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
                        <!-- Button trigger for login form modal -->
                        <button type="button" class="btn btn-primary" data-bs-backdrop="false" data-bs-toggle="modal"
                            data-bs-target="#inlineForm">
                            + เพิ่มสินค้าใหม่
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">รหัสสินค้า</th>
                                    <th style="text-align: center;"> รูป </th>
                                    <th style="text-align: center;">ชื่อสินค้า</th>
                                    <th style="text-align: center;">จำนวน</th>
                                    <th style="text-align: center;">จำนวนขั้นต่ำ</th>
                                    <th style="text-align: center;">ราคา(บาท)</th>
                                    <th style="text-align: center;">หน่วย</th>
                                    <th style="text-align: center;">ประเภท</th>
                                    <th style="text-align: center;">สถานะ</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['prod_id']; ?>">
                                        <td align="center"><?php echo $row['prod_id']; ?></td>
                                        <td align="center">
                                            <img src="photo/<?php echo $row['prod_img']; ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10%;">
                                        </td>
                                        <td align="center"><?php echo $row['prod_name']; ?></td>
                                        <td align="right" style="color: <?php echo ($row['prod_amount'] <= $row['prod_amount_min']) ? 'red' : ''; ?>;">
                                            <?php echo $row['prod_amount']; ?>
                                        </td>
                                        <td align="right"><?php echo $row['prod_amount_min']; ?></td>
                                        <td align="right"><?php echo $row['prod_price']; ?></td>
                                        <td align="center"><?php echo $row['prod_unit']; ?></td>
                                        <td align="center"><?php echo $row['prod_type_desc']; ?></td>
                                        <td align="center">
                                            <?php
                                            // Determine the badge class based on the status
                                            $badge_class = ($row['prod_status_desc'] === 'กำลังใช้งาน') ? 'badge bg-success' : 'badge bg-danger';
                                            ?>
                                            <span class="<?php echo $badge_class; ?>"><?php echo $row['prod_status_desc']; ?></span>
                                        </td>
                                        <td align="center">
                                            <a href="add_cart.php?prod_id=<?php echo $row['prod_id']; ?>" class="btn btn-primary rounded-pill">เบิก</a>

                                            <a href="edit_product.php?prod_id=<?php echo $row['prod_id']; ?>" class="btn btn-warning rounded-pill">แก้ไข</a>

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

        <!-- Add Product form Modal l -->
        <div class="modal fade text-left" id="inlineForm" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">เพิ่มสินค้าใหม่</h4>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>ชื่อสินค้า: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_name" placeholder="กรุณากรอกชื่อสินค้า"
                                            class="form-control">
                                    </div>
                                    <label>จำนวนสินค้า: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้า"
                                            class="form-control">
                                    </div>
                                    <label>จำนวนสินค้าขั้นต่ำ: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_min" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้าขั้นต่ำ"
                                            class="form-control">
                                    </div>
                                    <label>ราคา(บาท): </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_price" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้า"
                                            class="form-control">
                                    </div>
                                    <label>หน่วย: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_unit" placeholder="กรุณากรอกหน่วยสินค้า"
                                            class="form-control">
                                    </div>
                                    <label>ประเภท: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_type">
                                            <option value="" selected>กรุณาเลือกประเภทสินค้า</option> <!-- Default option -->
                                            <?php
                                            while ($type = $result_type->fetch_assoc()) :
                                            ?>
                                                <option value="<?php echo $type['prod_type_id']; ?>"><?php echo $type['prod_type_desc']; ?></option>
                                            <?php endwhile ?>
                                        </select>
                                    </div>
                                    <label>สถานะ: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_status">
                                            <option value="" selected>กรุณาเลือกสถานะสินค้า</option> <!-- Default option -->
                                            <?php
                                            while ($status = $result_status->fetch_assoc()) :
                                            ?>
                                                <option value="<?php echo $status['prod_status']; ?>"><?php echo $status['prod_status_desc']; ?></option>
                                            <?php endwhile ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="formFile" class="form-label"> รูปสินค้า </label>
                                        <input class="form-control" type="file" id="formFile" accept="image/jpeg, image/jpg, image/png">

                                        <!-- Image preview -->
                                        <img id="imagePreview" src="" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">

                                        <small class="form-text text-muted">Allowed file types: jpeg, jpg, png only / อัพโหลดรูปภาพได้แค่ jpeg, jpg, png เท่านั้น</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="redirectToAddProduct()">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">เพิ่มสินค้าหลายชิ้น</span>
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">ยกเลิก</span>
                                    </button>
                                    <button type="button" class="btn btn-success ml-1" onclick="addProduct()">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">เพิ่ม</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Product form Modal -->



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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
    <script src="assets/js/main.js"></script>




    <script>
        //ฟังชันลบสินค้า
        function deleteProduct(prod_id) {
            event.preventDefault();
            Swal.fire({
                title: 'คุณแน่ใจไหม?',
                text: "จะไม่สามารถแก้ไขอะไรได้อีก!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่  ลบเลย!',
                cancelButtonText: 'ยกเลิก'
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


        function addProduct() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Form validation checks
            if ($('#prod_name').val() == "") {
                $('#prod_name').addClass('is-invalid');
                $('#nameFeedback').text("Name is empty.");
                isValid = false;
            }
            if ($('#prod_amount').val() == "") {
                $('#prod_amount').addClass('is-invalid');
                $('#amountFeedback').text("Amount is empty.");
                isValid = false;
            }
            if ($('#prod_amount_min').val() == "") {
                $('#prod_amount_min').addClass('is-invalid');
                $('#amountMinFeedback').text("Amount Min is empty.");
                isValid = false;
            }
            if ($('#prod_price').val() == "") {
                $('#prod_price').addClass('is-invalid');
                $('#priceFeedback').text("Price is empty.");
                isValid = false;
            }
            if ($('#prod_unit').val() == "") {
                $('#prod_unit').addClass('is-invalid');
                $('#unitFeedback').text("Unit is empty.");
                isValid = false;
            }
            if ($('#prod_type').val() == "") {
                $('#prod_type').addClass('is-invalid');
                $('#typeFeedback').text("Type is empty.");
                isValid = false;
            }
            if ($('#prod_status').val() == "") {
                $('#prod_status').addClass('is-invalid');
                $('#statusFeedback').text("Status is empty.");
                isValid = false;
            }

            let fileUploaded = $('#formFile')[0].files.length > 0;

            if (isValid) {
                let formData = new FormData();
                if (fileUploaded) {
                    // Append the image file if uploaded
                    formData.append('img', $('#formFile')[0].files[0]);
                }
                formData.append('name', $('#prod_name').val());
                formData.append('amount', $('#prod_amount').val());
                formData.append('amount_min', $('#prod_amount_min').val());
                formData.append('price', $('#prod_price').val());
                formData.append('unit', $('#prod_unit').val());
                formData.append('type', $('#prod_type').val());
                formData.append('status', $('#prod_status').val());

                $.ajax({
                    url: "/Final_Project/api/api_add_pd.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false, // Prevent jQuery from converting the FormData object into a query string
                    contentType: false, // Prevent jQuery from overriding the content type
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'เพิ่มสินค้าสำเร็จ!',
                                icon: 'success'
                            }).then(() => {
                                    window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "เพิ่มสินค้าผิดพลาด!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                // Validation failed, show an error message and keep the modal open
                Swal.fire({
                    title: "เพิ่มสินค้าผิดพลาด!",
                    text: "กรุณากรอกข้อมูลให้ครบถ้วน",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
            }

            if (!fileUploaded && isValid) {
                // Close the modal if no file was uploaded and the form is valid
                $('#inlineForm').modal('hide');
            }
        }




        // Handle image preview when a file is selected
        document.getElementById('formFile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');

            // Check if a file is selected and is an image
            if (file && (file.type === "image/jpeg" || file.type === "image/jpg" || file.type === "image/png")) {
                const reader = new FileReader();

                // Once the file is read, set it as the src for the img element
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }

                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                // Display a default image or reset to the previous image
                imagePreview.src = 'photo/no_img.jpg'; // Change this to the appropriate path
            }
        });

        // ไปหน้า add_product.php
        function redirectToAddProduct() {
            window.location.href = 'add_product.php';
        }
    </script>




    </body>

    </html>

<?php
} else {
    header("location: error_user_page.php");
}
?>