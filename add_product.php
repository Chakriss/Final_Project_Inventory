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

    if($_SESSION["user_stock"] == 1){
        $stock_pd = "stock_it.php";
    }else{
        $stock_pd = "stock_hr.php";
    }

    //เรียกใช้ฟังชันดึงประเภทสินค้า
    $result_type = selectType($conn);

    //เรียกใช้ฟังชันดึงสถานะสินค้า
    $result_status = selectStatus($conn);
    $productlist = 0;

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
                                            <input type="text" id="prod_name_0" placeholder="กรุณากรอกชื่อสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="nameFeedback_0"></div>
                                        </div>
                                        <label>จำนวนสินค้า: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount_0" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="amountFeedback"></div>
                                        </div>
                                        <label>จำนวนสินค้าขั้นต่ำ: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount_min_0" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้าขั้นต่ำ" class="form-control">
                                            <div class="invalid-feedback" id="amountMinFeedback_0"></div>
                                        </div>
                                        <label>ราคา(บาท): </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_price_0" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกราคา" class="form-control">
                                            <div class="invalid-feedback" id="priceFeedback_0"></div>
                                        </div>
                                        <label>หน่วย: </label>
                                        <div class="form-group">
                                            <input type="text" id="prod_unit_0" placeholder="กรุณากรอกหน่วยสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="unitFeedback_0"></div>
                                        </div>
                                        <label>ประเภท: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_type_0">
                                                <option value="" selected>กรุณาเลือกประเภทสินค้า</option>
                                                <?php foreach ($types as $type) : ?>
                                                    <option value="<?php echo $type['prod_type_id']; ?>"><?php echo $type['prod_type_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="typeFeedback_0"></div>
                                        </div>
                                        <label>สถานะ: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_status_0">
                                                <option value="" selected>กรุณาเลือกสถานะสินค้า</option>
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?php echo $status['prod_status']; ?>"><?php echo $status['prod_status_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="statusFeedback_0"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="formFile_0" class="form-label"> รูปสินค้า </label>
                                            <input class="form-control" type="file" id="formFile_0" accept="image/jpeg, image/jpg, image/png">
                                            <img id="imagePreview_0" src="" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">
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
                        <button type="submit" class="btn btn-info btn-fw">เพิ่ม excel.csv</button>
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
            function addProductForm() {

                const productFormsContainer = document.getElementById('productFormsContainer');
                const formCount = document.querySelectorAll('.product-form-container').length;

                const newForm = `
                        <div class="product-form-container">
                            <h5>product ${formCount}</h5>
                            <div class="row product-form" id="product-form-${formCount}">
                                <div class="col-md-6">
                                    <label>ชื่อสินค้า: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_name_${formCount}" placeholder="กรุณากรอกชื่อสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="nameFeedback_${formCount}"></div>
                                    </div>
                                    <label>จำนวนสินค้า: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_${formCount}" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="amountFeedback_${formCount}"></div>
                                    </div>
                                    <label>จำนวนสินค้าขั้นต่ำ: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_min_${formCount}" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกจำนวนสินค้าขั้นต่ำ" class="form-control">
                                        <div class="invalid-feedback" id="amountMinFeedback_${formCount}"></div>
                                    </div>
                                    <label>ราคา(บาท): </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_price_${formCount}" min="0" oninput="validity.valid||(value='');" placeholder="กรุณากรอกราคา" class="form-control">
                                        <div class="invalid-feedback" id="priceFeedback_${formCount}"></div>
                                    </div>
                                    <label>หน่วย: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_unit_${formCount}" placeholder="กรุณากรอกหน่วยสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="unitFeedback_${formCount}"></div>
                                    </div>
                                    <label>ประเภท: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_type_${formCount}">
                                            <option value="" selected>กรุณาเลือกประเภทสินค้า</option>
                                            ${types.map(type => `<option value="${type.prod_type_id}">${type.prod_type_desc}</option>`).join('')}
                                        </select>
                                        <div class="invalid-feedback" id="typeFeedback_${formCount}"></div>
                                    </div>
                                    <label>สถานะ: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_status_${formCount}">
                                            <option value="" selected>กรุณาเลือกสถานะสินค้า</option>
                                            ${statuses.map(status => `<option value="${status.prod_status}">${status.prod_status_desc}</option>`).join('')}
                                        </select>
                                        <div class="invalid-feedback" id="statusFeedback_${formCount}"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="formFile${formCount}" class="form-label"> รูปสินค้า </label>
                                        <input class="form-control" type="file" id="formFile_${formCount}" accept="image/jpeg, image/jpg, image/png">
                                        <img id="imagePreview_${formCount}" src="" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">
                                        <small class="form-text text-muted">Allowed file types: jpeg, jpg, png only / อัพโหลดรูปภาพได้แค่ jpeg, jpg, png เท่านั้น</small>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeProductForm('product-form-${formCount}')">ลบฟอร์ม</button>
                                </div>
                            </div>
                            <hr> <!-- Add a horizontal line between product forms -->
                        </div>
                    `;

                productFormsContainer.insertAdjacentHTML('beforeend', newForm);

                // Add event listener to the new file input for image preview
                document.getElementById(`formFile_${formCount}`).addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const imagePreview = document.getElementById(`imagePreview_${formCount}`);

                    if (file && (file.type === "image/jpeg" || file.type === "image/jpg" || file.type === "image/png")) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.src = 'photo/no_img.jpg';
                    }
                });
            }

            // Handle image preview when a file is selected
            document.getElementById('formFile_0').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = document.getElementById('imagePreview_0');

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


            function removeProductForm(formId) {
                const formElement = document.getElementById(formId);
                if (formElement) {
                    formElement.closest('.product-form-container').remove();
                }
            }


            function addProduct(event) {
                event.preventDefault(); // Prevent the form from submitting
                let isValid = true;

                // Reset validation messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                // Array to hold AJAX requests
                let ajaxRequests = [];

                // Loop through each product form
                $('.product-form').each(function(index) {
                    const prodName = $(this).find(`#prod_name_${index}`);
                    const prodAmount = $(this).find(`#prod_amount_${index}`);
                    const prodAmountMin = $(this).find(`#prod_amount_min_${index}`);
                    const prodPrice = $(this).find(`#prod_price_${index}`);
                    const prodUnit = $(this).find(`#prod_unit_${index}`);
                    const prodType = $(this).find(`#prod_type_${index}`);
                    const prodStatus = $(this).find(`#prod_status_${index}`);
                    const formFile = $(this).find(`#formFile_${index}`);

                    // Perform validation checks
                    if (prodName.val() == "") {
                        prodName.addClass('is-invalid');
                        $(this).find(`#nameFeedback_${index}`).text("Name is empty.");
                        isValid = false;
                    }
                    if (prodAmount.val() == "") {
                        prodAmount.addClass('is-invalid');
                        $(this).find(`#amountFeedback_${index}`).text("Amount is empty.");
                        isValid = false;
                    }
                    if (prodAmountMin.val() == "") {
                        prodAmountMin.addClass('is-invalid');
                        $(this).find(`#amountMinFeedback_${index}`).text("Amount Min is empty.");
                        isValid = false;
                    }
                    if (prodPrice.val() == "") {
                        prodPrice.addClass('is-invalid');
                        $(this).find(`#priceFeedback_${index}`).text("Price is empty.");
                        isValid = false;
                    }
                    if (prodUnit.val() == "") {
                        prodUnit.addClass('is-invalid');
                        $(this).find(`#unitFeedback_${index}`).text("Unit is empty.");
                        isValid = false;
                    }
                    if (prodType.val() == "") {
                        prodType.addClass('is-invalid');
                        $(this).find(`#typeFeedback_${index}`).text("Type is empty.");
                        isValid = false;
                    }
                    if (prodStatus.val() == "") {
                        prodStatus.addClass('is-invalid');
                        $(this).find(`#statusFeedback_${index}`).text("Status is empty.");
                        isValid = false;
                    }

                    if (isValid) {
                        let formData = new FormData();
                        formData.append('name', prodName.val());
                        formData.append('amount', prodAmount.val());
                        formData.append('amount_min', prodAmountMin.val());
                        formData.append('price', prodPrice.val());
                        formData.append('unit', prodUnit.val());
                        formData.append('type', prodType.val());
                        formData.append('status', prodStatus.val());

                        // Check if a file was uploaded
                        if (formFile[0] && formFile[0].files.length > 0) {
                            formData.append('img', formFile[0].files[0]);
                        }

                        // Store AJAX request promises
                        ajaxRequests.push(
                            $.ajax({
                                url: "/Final_Project/api/api_add_pd_2.php",
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: "json"
                            })
                        );
                    } else {
                        Swal.fire({
                            title: "เพิ่มสินค้าผิดพลาด!",
                            text: "โปรดตรวจสอบว่ามีข้อมูลใดไม่ถูกต้อง",
                            icon: "error"
                        });
                    }
                });

                // Process all AJAX requests
                $.when.apply($, ajaxRequests).done(function() {
                    Swal.fire({
                        title: 'เพิ่มสินค้าสำเร็จ!',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '<?php echo $stock_pd; ?>';
                    });
                }).fail(function() {
                    Swal.fire({
                        title: "เพิ่มสินค้าผิดพลาด!",
                        text: "เกิดข้อผิดพลาดในการเพิ่มสินค้า",
                        icon: "error"
                    });
                });
            }
        </script>

    <?php
} else {
    header("location: error_user_page.php");
}
    ?>