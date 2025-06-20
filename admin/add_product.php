<?php
session_start();
include_once '../config/function.php';
include_once '../header.php';
include_once 'menu_admin.php';
include_once '../navbar.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 1 || $_SESSION["user_stock"] == 2)) {

    if ($_SESSION["user_stock"] == 1) {
        $stock_pd = "stock_it.php";
    } else {
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
    <style>
        .hidden {
            display: none;
        }
    </style>
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
                        <h3>Add Product</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header" style="margin-bottom: 20px;">
                        <h5>product</h5>
                    </div>
                    <div class="card-body">
                        <div id="productFormsContainer">
                            <div class="product-form-container">
                                <div class="row product-form" id="product-form-0">
                                    <div class="col-md-6">
                                        <label>Name: </label>
                                        <div class="form-group">
                                            <input type="text" id="prod_name_0" placeholder="Please enter name / กรุณากรอกชื่อสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="nameFeedback_0"></div>
                                        </div>
                                        <label>Quantity: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount_0" min="0" oninput="validity.valid||(value='');" placeholder="Please enter quantity / กรุณากรอกจำนวนสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="amountFeedback"></div>
                                        </div>
                                        <label>Quantity Minimum: </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_amount_min_0" min="1" oninput="validity.valid||(value='');" placeholder="Please enter quantity min / กรุณากรอกจำนวนสินค้าขั้นต่ำ" class="form-control">
                                            <div class="invalid-feedback" id="amountMinFeedback_0"></div>
                                        </div>
                                        <label>Price(baht): </label>
                                        <div class="form-group">
                                            <input type="number" id="prod_price_0" min="0" oninput="validity.valid||(value='');" placeholder="Please enter price / กรุณากรอกราคา(บาท)" class="form-control">
                                            <div class="invalid-feedback" id="priceFeedback_0"></div>
                                        </div>
                                        <label>Unit: </label>
                                        <div class="form-group">
                                            <input type="text" id="prod_unit_0" placeholder="Please enter unit / กรุณากรอกหน่วยสินค้า" class="form-control">
                                            <div class="invalid-feedback" id="unitFeedback_0"></div>
                                        </div>
                                        <label>Detail: </label>
                                        <div class="form-group">
                                            <textarea class="form-control" id="prod_detail_0" placeholder="Please enter detail / กรุณากรอกรายละเอียดสินค้า" rows="3"></textarea>
                                            <div class="invalid-feedback" id="detailFeedback_0"></div>
                                        </div>
                                        <label>Product Type: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_type_0">
                                                <option value="" selected>Please enter type / กรุณาเลือกประเภทสินค้า</option>
                                                <?php foreach ($types as $type) : ?>
                                                    <option value="<?php echo $type['prod_type_id']; ?>"><?php echo $type['prod_type_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="typeFeedback_0"></div>
                                        </div>
                                        <label>Product Status: </label>
                                        <div class="form-group">
                                            <select class="form-select" id="prod_status_0">
                                                <option value="" selected>Please enter status / กรุณาเลือกสถานะสินค้า</option>
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?php echo $status['prod_status']; ?>"><?php echo $status['prod_status_desc']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="invalid-feedback" id="statusFeedback_0"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="formFile_0" class="form-label"> Photo </label>
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
                        <button type="button" class="btn btn-primary btn-fw" onclick="addProductForm()">Add product form</button>
                        <button type="submit" class="btn btn-success btn-fw" data-bs-backdrop="false"
                            data-bs-toggle="modal" data-bs-target="#modalcsv"><i class="bi bi-filetype-csv"></i> Import CSV</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger btn-fw" onclick="window.location.href='<?php echo $stock_pd ?>';">Cancle</button>
                        <button type="submit" class="btn btn-success btn-fw" onclick="addProduct(event)">Add</button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Add Modal csv -->
        <div class="modal fade text-left" id="modalcsv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">Add Product From CSV</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form id="csvUploadForm" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <h4>Example:</h4>
                                    <img id="cart_img" src="../assets/images/general/Example_csv.png" alt="Image"
                                        style="display:block; margin-top:10px; max-width: 100%; height: auto;">
                                </div>

                                <div class="form-group">
                                    <label for="formFile" class="form-label">
                                        <h4>CSV files:</h4>
                                    </label>
                                    <input class="form-control" type="file" id="formFileCsv" accept=".csv" name="file">
                                    <small class="form-text text-muted">Allowed file types: csv only / อัพโหลดไฟล์ได้แค่ csv เท่านั้น</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between w-100">
                            <div>
                                <button type="button" class="btn btn-primary" onclick="CsvExport()">
                                    <span class="d-none d-sm-block">Example</span>
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <span class="d-none d-sm-block">Cancel</span>
                                </button>
                                <button type="button" class="btn btn-success ml-1" onclick="addCSV()">
                                    <span class="d-none d-sm-block">Confirm</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Modal csv -->

        <!-- <------Export ตัวอย่าง csv------>
        <table id="productTable" class="hidden">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Quantity Minimum</th>
                    <th>Price (baht)</th>
                    <th>Unit</th>
                    <th>Detail</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>เมาส์ L</td>
                    <td>10</td>
                    <td>5</td>
                    <td>400</td>
                    <td>ชิ้น</td>
                    <td>Logitech</td>
                    <td>อุปกรณ์คอมพิวเตอร์</td>
                </tr>
                <tr>
                    <td>คีย์บอร์ด D</td>
                    <td>20</td>
                    <td>10</td>
                    <td>500</td>
                    <td>ชิ้น</td>
                    <td>Dell</td>
                    <td>อุปกรณ์คอมพิวเตอร์</td>
                </tr>
                <tr>
                    <td>หูฟัง H</td>
                    <td>30</td>
                    <td>15</td>
                    <td>1000</td>
                    <td>ชิ้น</td>
                    <td>HyperX</td>
                    <td>อุปกรณ์คอมพิวเตอร์</td>
                </tr>
            </tbody>
        </table>
        <!-- <------Export ตัวอย่าง csv------>


        <?php include_once '../footer.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                attachFileInputListeners();
            });
            //แสดงรูปที่ฟอร์มเดิม
            function attachFileInputListeners() {
                // Attach listeners to all existing file inputs
                document.querySelectorAll('input[type="file"]').forEach(fileInput => {
                    fileInput.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        const fileInputId = event.target.id;
                        const formCount = fileInputId.split('_').pop(); // Extract the form index from the input ID
                        const imagePreview = document.getElementById(`imagePreview_${formCount}`);

                        if (file && (file.type === "image/jpeg" || file.type === "image/jpg" || file.type === "image/png")) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                imagePreview.src = e.target.result;
                            }

                            reader.readAsDataURL(file);
                        } else {
                            imagePreview.src = 'photo/no_img.jpg'; // Change this to the appropriate path
                        }
                    });
                });
            }

            //เพิ่มฟอร์มใหม่
            function addProductForm() {
                const productFormsContainer = document.getElementById('productFormsContainer');
                const formCount = document.querySelectorAll('.product-form-container').length;

                const newForm = `
                            <div class="product-form-container">
                                <div class="card-header" style="margin-bottom: 20px;">
                                    <h5>product ${formCount}</h5>
                                </div>
                                <div class="row product-form" id="product-form-${formCount}">
                                    <div class="col-md-6">
                                    <label>Name: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_name_${formCount}" placeholder="Please enter name / กรุณากรอกชื่อสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="nameFeedback_${formCount}"></div>
                                    </div>
                                    <label>Quantity: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_${formCount}" min="0" oninput="validity.valid||(value='');" placeholder="Please enter quantity / กรุณากรอกจำนวนสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="amountFeedback_${formCount}"></div>
                                    </div>
                                    <label>Quantity Minimum: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_min_${formCount}" min="1" oninput="validity.valid||(value='');" placeholder="Please enter quantity Min / กรุณากรอกจำนวนสินค้าขั้นต่ำ" class="form-control">
                                        <div class="invalid-feedback" id="amountMinFeedback_${formCount}"></div>
                                    </div>
                                    <label>Price(baht): </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_price_${formCount}" min="0" oninput="validity.valid||(value='');" placeholder="Please enter price / กรุณากรอกราคา(บาท)" class="form-control">
                                        <div class="invalid-feedback" id="priceFeedback_${formCount}"></div>
                                    </div>
                                    <label>Unit: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_unit_${formCount}" placeholder="Please enter unit / กรุณากรอกหน่วยสินค้า" class="form-control">
                                        <div class="invalid-feedback" id="unitFeedback_${formCount}"></div>
                                    </div>
                                    <label>Detail: </label>
                                        <div class="form-group">
                                            <textarea class="form-control" id="prod_detail_${formCount}" placeholder="Please enter detail / กรุณากรอกรายละเอียดสินค้า" rows="3"></textarea>
                                            <div class="invalid-feedback" id="detailFeedback_${formCount}"></div>
                                        </div>
                                    <label>Product Type: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_type_${formCount}">
                                            <option value="" selected>Please enter type / กรุณาเลือกประเภทสินค้า</option>
                                            ${types.map(type => `<option value="${type.prod_type_id}">${type.prod_type_desc}</option>`).join('')}
                                        </select>
                                        <div class="invalid-feedback" id="typeFeedback_${formCount}"></div>
                                    </div>
                                    <label>Product Status: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_status_${formCount}">
                                            <option value="" selected>Please enter status / กรุณาเลือกสถานะสินค้า</option>
                                            ${statuses.map(status => `<option value="${status.prod_status}">${status.prod_status_desc}</option>`).join('')}
                                        </select>
                                        <div class="invalid-feedback" id="statusFeedback_${formCount}"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="formFile_${formCount}" class="form-label"> Photo </label>
                                            <input class="form-control" type="file" id="formFile_${formCount}" accept="image/jpeg, image/jpg, image/png">
                                            <img id="imagePreview_${formCount}" src="" alt="Image Preview" style="display:block; margin-top:10px; max-width: 100%; height: auto;">
                                            <small class="form-text text-muted">Allowed file types: jpeg, jpg, png only / อัพโหลดรูปภาพได้แค่ jpeg, jpg, png เท่านั้น</small>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeProductForm('product-form-${formCount}')">Delete Form</button>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        `;


                productFormsContainer.insertAdjacentHTML('beforeend', newForm);

                // Attach event listener for the new file input
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
                        imagePreview.src = 'photo/no_img.jpg'; // Change this to the appropriate path
                    }
                });
            }


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

                let formDataArray = [];

                // Loop through each product form
                $('.product-form').each(function(index) {
                    const prodName = $(this).find(`#prod_name_${index}`);
                    const prodAmount = $(this).find(`#prod_amount_${index}`);
                    const prodAmountMin = $(this).find(`#prod_amount_min_${index}`);
                    const prodPrice = $(this).find(`#prod_price_${index}`);
                    const prodUnit = $(this).find(`#prod_unit_${index}`);
                    const prodDetail = $(this).find(`#prod_detail_${index}`);
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
                        $(this).find(`#amountFeedback_${index}`).text("Quantity is empty.");
                        isValid = false;
                    }
                    if (prodAmountMin.val() == "") {
                        prodAmountMin.addClass('is-invalid');
                        $(this).find(`#amountMinFeedback_${index}`).text("Quantity Min is empty.");
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
                    if (prodDetail.val() == "") {
                        prodDetail.addClass('is-invalid');
                        $(this).find(`#detailFeedback_${index}`).text("detail is empty.");
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
                        formData.append('detail', prodDetail.val());
                        formData.append('type', prodType.val());
                        formData.append('status', prodStatus.val());
                        // Check if a file was uploaded
                        if (formFile[0] && formFile[0].files.length > 0) {
                            formData.append('img', formFile[0].files[0]);
                        }

                        formDataArray.push(formData);
                    } else {
                        Swal.fire({
                            title: "Wrong product added!",
                            text: "Please check if any information is incorrect.",
                            icon: "error"
                        });
                    }
                });

                // Send AJAX requests for each formData
                if (formDataArray.length > 0) {
                    let requests = formDataArray.map(formData => {
                        return $.ajax({
                            url: "/Final_Project/api/api_add_pd_2.php",
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json"
                        });
                    });

                    $.when.apply($, requests).done(function() {
                        // All requests completed
                        Swal.fire({
                                title: 'Add product success!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            })
                            .then((result) => {
                                window.location.href = '<?php echo $stock_pd; ?>';
                            });
                    }).fail(function() {
                        Swal.fire({
                            title: "Wrong product added!",
                            text: "There was an error adding the products.",
                            icon: "error"
                        });
                    });
                }
            }


            function addCSV() {
                const fileInput = $('#formFileCsv')[0];
                const file = fileInput.files[0];

                if (!file) {
                    Swal.fire({
                        title: 'No file selected',
                        text: 'Please select a CSV file to upload.',
                        icon: 'error'
                    });
                    return;
                }

                // ตรวจสอบไฟล์ว่ามีประเภทเป็น CSV
                if (file.type !== 'application/vnd.ms-excel' && !file.name.endsWith('.csv')) {
                    Swal.fire({
                        title: 'Invalid file type',
                        text: 'Please upload a CSV file only.',
                        icon: 'error'
                    });
                    return;
                }

                let formData = new FormData();
                formData.append('file', file);

                // ส่งข้อมูลผ่าน AJAX
                $.ajax({
                    url: '/Final_Project/api/api_import_csv.php', // ไฟล์ PHP สำหรับประมวลผล CSV
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Add product success!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?php echo $stock_pd; ?>';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Upload Failed',
                            text: 'There was an error uploading the CSV file.',
                            icon: 'error'
                        });
                    }
                });
            }

            function CsvExport() {
                // ดึงตารางจาก DOM
                var table = document.getElementById("productTable");
                var rows = table.querySelectorAll("tr");

                // สร้าง array เพื่อเก็บข้อมูล CSV
                var csvContent = "\uFEFF"; // ใส่ BOM ตอนเริ่มไฟล์

                // Loop ผ่านแต่ละ row ในตาราง
                rows.forEach(function(row) {
                    var rowData = [];
                    var cols = row.querySelectorAll("th, td");

                    // เก็บข้อมูลแต่ละ cell ใน array
                    cols.forEach(function(col) {
                        rowData.push('"' + col.innerText + '"'); // ใส่เครื่องหมาย " เพื่อป้องกันข้อมูลที่มี , 
                    });

                    // รวมข้อมูลในแต่ละ row ด้วยการคั่น , และขึ้นบรรทัดใหม่
                    csvContent += rowData.join(",") + "\n";
                });

                // สร้าง blob จากข้อมูล CSV
                var blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                var link = document.createElement("a");
                var url = URL.createObjectURL(blob);

                link.setAttribute("href", url);
                link.setAttribute("download", "Example_data.csv");
                link.style.visibility = 'hidden';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        </script>

    <?php
} else {
    header("location: ../admin/error_admin_page.php");
}
    ?>