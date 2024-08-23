<?php
session_start();
include_once 'config/connect_db.php';
include_once 'config/function.php';

include_once 'header.php';
include_once 'menu_admin.php';
include_once 'navbar.php';

if (empty($_GET["prod_id"])) {
    echo "<script type='text/javascript'>";
    echo "alert('เกิดข้อผิดพลาด กรุณาเลือกสินค้าก่อน!!');";
    echo "window.location = 'stock_it.php'; ";
    echo "</script>";
}

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    $prod_id = $_GET['prod_id'];

    //เรียกใช้ฟังชันดึงข้อมูลสินค้า
    $result = editProduct($conn, $prod_id);
    $row = $result->fetch_assoc();

    //เรียกใช้ฟังชันดึงประเภทสินค้า
    $result_type = selectType($conn);

    //เรียกใช้ฟังชันดึงสถานะสินค้า
    $result_status = selectStatus($conn);

?>
    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>แก้ไขสินค้า</h3>
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
                                    <label for="basicInput">รหัสสินค้า</label>
                                    <input type="text" class="form-control" id="prod_id" value="<?php echo $row['prod_id']; ?>" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">ชื่อสินค้า</label>
                                    <input type="text" class="form-control" id="prod_name" value="<?php echo $row['prod_name']; ?>">
                                    <div class="invalid-feedback" id="nameFeedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">จำนวนสินค้า</label>
                                    <input type="text" class="form-control" id="prod_amount" value="<?php echo $row['prod_amount']; ?>">
                                    <div class="invalid-feedback" id="amountFeedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">จำนวนสินค้าขั้นต่ำ</label>
                                    <input type="text" class="form-control" id="prod_amount_min" value="<?php echo $row['prod_amount_min']; ?>">
                                    <div class="invalid-feedback" id="amountMinFeedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">ราคาสินค้า(บาทต่อหน่วย)</label>
                                    <input type="text" class="form-control" id="prod_price" value="<?php echo $row['prod_price']; ?>">
                                    <div class="invalid-feedback" id="priceFeedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="basicInput">หน่วย</label>
                                    <input type="text" class="form-control" id="prod_unit" value="<?php echo $row['prod_unit']; ?>">
                                    <div class="invalid-feedback" id="unitFeedback"></div>
                                </div>


                                <div class="form-group">
                                    <label for="basicInput">ประเภทสินค้า</label>
                                    <select class="choices form-select" id="prod_type">
                                        <?php
                                        $selected_type = $row['prod_type_id'];
                                        while ($type = $result_type->fetch_assoc()) :
                                            $selected = ($type['prod_type_id'] == $selected_type) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $type['prod_type_id']; ?>" <?php echo $selected; ?>><?php echo $type['prod_type_desc']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="typeFeedback"></div>
                                </div>


                                <div class="form-group">
                                    <label for="basicInput">สถานะสินค้า</label>
                                    <select class="choices form-select" id="prod_status">
                                        <?php
                                        $selected_status = $row['prod_status'];
                                        while ($status = $result_status->fetch_assoc()) :
                                            $select = ($status['prod_status'] == $selected_status) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $status['prod_status']; ?>" <?php echo $select; ?>><?php echo $status['prod_status_desc']; ?></option>
                                        <?php endwhile ?>
                                    </select>
                                    <div class="invalid-feedback" id="statusFeedback"></div>
                                </div>
                            </div>


                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="formFile" class="form-label"> รูปสินค้า </label>
                                    <input class="form-control" type="file" id="formFile" accept="image/jpeg, image/jpg, image/png">

                                    <!-- Image preview -->
                                    <img id="imagePreview" src="photo/<?= $row['prod_img']; ?>" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">

                                    <small class="form-text text-muted">Allowed file types: jpeg, jpg, png only / อัพโหลดรูปภาพได้แค่ jpeg, jpg, png เท่านั้น</small>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <button type="button" class="btn btn-secondary btn-fw" onclick="window.location.href='stock_it.php';">ยกเลิก</button>
                <button type="submit" class="btn btn-success btn-fw" onclick="update_product()">อัพเดท</button>

            </section>

        </div>


        <?php
        include_once 'footer.php';
        ?>

        <script>
            function update_product() {
                event.preventDefault();
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
                        url: "/Final_Project/api/api_update_pd.php",
                        type: 'POST',
                        dataType: "json",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.status === "successfully") {
                                Swal.fire({
                                        title: 'แก้ไข สำเร็จ!',
                                        icon: 'success'
                                    })
                                    .then((result) => {
                                        window.location.href = 'stock_it.php';
                                    });
                            } else {
                                Swal.fire({
                                    title: "แก้ไข ผิดพลาด!",
                                    text: result.message,
                                    icon: "error"
                                });
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        title: "แก้ไขผิดพลาด!",
                        text: "โปรดตรวจสอบว่ามีข้อมูลใดไม่ถูกต้อง",
                        icon: "error"
                    });
                }
            }





            //show รูปภาพขึ้นหน้าจอ
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
                    imagePreview.src = 'photo/<?= $row['prod_img']; ?>'; // Reset to old image if the new file is not valid
                }
            });
        </script>


    <?php
} else {
    header("location: error_user_page.php");
}
    ?>