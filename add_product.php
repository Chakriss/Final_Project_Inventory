<?php
session_start();
include_once 'config/connect_db.php';
include_once 'config/function.php';

include_once 'header.php';
include_once 'menu_admin.php';
include_once 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    //เรียกใช้ฟังชันดึงประเภทสินค้า
    $result_type = selectType($conn);

    //เรียกใช้ฟังชันดึงสถานะสินค้า
    $result_status = selectStatus($conn);
    $productlist = 1;

    $types = [];
    while ($type = $result_type->fetch_assoc()) {
        $types[] = $type;
    }

    $statuses = [];
    while ($status = $result_status->fetch_assoc()) {
        $statuses[] = $status;
    }

?>
    <script>
        // Pass PHP data to JavaScript
        const types = <?php echo json_encode($types); ?>;
        const statuses = <?php echo json_encode($statuses); ?>;
    </script>

    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>เพิ่มสินค้า</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5>product <?php echo $productlist ?></h5>
                    </div>
                    <div class="card-body">
                        <div id="productFormsContainer">
                            <div class="product-form-container">
                                <div class="row product-form" id="product-form-0">
                                    <div class="col-md-6">
                                        <label>ชื่อสินค้า: </label>
                                        <div class="form-group">
                                            <input type="text" id="prod_name" placeholder="กรุณากรอกชื่อสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="nameFeedback"></div>
                                        </div>
                                        <label>จำนวนสินค้า: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="amountFeedback"></div>
                                        </div>
                                        <label>จำนวนสินค้าขั้นต่ำ: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount_min" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้าขั้นต่ำ" class="form-control">
                                            <div class="invalid-feedback" id="amountMinFeedback"></div>
                                        </div>
                                        <label>ราคา(บาท): </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_price" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกราคา" class="form-control">
                                            <div class="invalid-feedback" id="priceFeedback"></div>
                                        </div>
                                        <label>หน่วย: </label>
                                        <div class="form-group">
                                            <input type="text" id="prod_unit" placeholder="กรุณากรอกหน่วยสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="unitFeedback"></div>
                                        </div>
                                        <label>ประเภท: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_type">
                                                <option value="" selected>กรุณาเลือกประเภทสินค้า</option>
                                                <?php foreach ($types as $type) : ?>
                                                    <option value="<?php echo $type['prod_type_id']; ?>"><?php echo $type['prod_type_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="typeFeedback"></div>
                                        </div>
                                        <label>สถานะ: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_status">
                                                <option value="" selected>กรุณาเลือกสถานะสินค้า</option>
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?php echo $status['prod_status']; ?>"><?php echo $status['prod_status_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="statusFeedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="formFile0" class="form-label"> รูปสินค้า </label>
                                            <input class="form-control" type="file" id="formFile" accept="image/jpeg, image/jpg, image/png">
                                            <img id="imagePreview" src="" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">
                                            <small class="form-text text-muted">Allowed file types: jpeg, jpg, png only / อัพโหลดรูปภาพได้แค่ jpeg, jpg, png เท่านั้น</small>
                                        </div>
                                        <!-- <button type="button" class="btn btn-danger btn-sm" onclick="removeProductForm('product-form-0')">ลบฟอร์ม</button> -->
                                    </div>
                                </div>
                                <hr> <!-- Add a horizontal line between product forms -->
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between w-100">
                    <div>
                        <button type="button" class="btn btn-primary btn-fw" onclick="addProductForm()">เพิ่มฟอร์มสินค้า</button>
                        <button type="submit" class="btn btn-success btn-fw">เพิ่ม excel.csv</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger btn-fw" onclick="window.location.href='stock_it.php';">ยกเลิก</button>
                        <button type="submit" class="btn btn-success btn-fw" onclick="addProduct(event)">เพิ่ม</button>
                    </div>
                </div>
            </section>
        </div>

        <?php include_once 'footer.php'; ?>

        <script>
            //แสดงรูปที่เลือก
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

            function addProductForm() {

            }



            function addProduct(event) {
                event.preventDefault(); // Prevent the form from submitting
                let isValid = true;
                // Reset validation messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

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


                if (isValid) {
                    let formData = new FormData();
                    formData.append('id', $('#prod_id').val());
                    formData.append('img', $('#formFile')[0].files[0]);
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
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(result) {
                            if (result.status === "successfully") {
                                Swal.fire({
                                    title: 'เพิ่มสินค้าสำเร็จ!',
                                    icon: 'success'
                                }).then(() => {
                                    window.location.href = 'stock_it.php';
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
                    Swal.fire({
                        title: "เพิ่มสินค้าผิดพลาด!",
                        text: "โปรดตรวจสอบว่ามีข้อมูลใดไม่ถูกต้อง",
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