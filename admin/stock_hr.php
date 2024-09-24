<?php

session_start();
// include_once 'config/connect_db.php';
include_once '../config/function.php';


// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_stock"]) && ($_SESSION["user_stock"] == 2 || $_SESSION["user_stock"] == 3)) {
    $stock = 2;
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


    <?php
    // Check the user level and include relevant files
    if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "A") {
        include_once 'menu_admin.php';
    } else {
        include_once 'menu_user.php';
    }
    include_once '../navbar.php';
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <?php if ($_SESSION["user_level"] !== "U") : ?>
                            <!-- Button trigger for Add Product form modal -->
                            <button type="button" class="btn btn-primary" data-bs-backdrop="false" data-bs-toggle="modal"
                                data-bs-target="#modalAddProduct">
                                + New Product
                            </button>
                        <?php endif; ?>
                        <a href="cart_hr.php" class="btn btn-warning">
                            <span class="fas fa-shopping-cart"></span>
                            <span id="cart_count">0</span></a>

                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Product ID</th>
                                    <th style="text-align: center;"> Photo </th>
                                    <th style="text-align: center;">Name</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">Unit</th>
                                    <th style="text-align: center;">Type</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['prod_id']; ?>">
                                        <td align="center"><?php echo $row['prod_id']; ?></td>
                                        <td align="center">
                                            <img src="../photo/<?php echo $row['prod_img']; ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10%;" onclick="expandImage('<?php echo $row['prod_img']; ?>')">
                                        </td>
                                        <td align="center"><?php echo $row['prod_name']; ?></td>
                                        <td align="center" style="color: <?php echo ($row['prod_amount'] <= $row['prod_amount_min']) ? 'red' : ''; ?>;">
                                            <?php echo $row['prod_amount']; ?>
                                        </td>
                                        <td align="center"><?php echo $row['prod_unit']; ?></td>
                                        <td align="center"><?php echo $row['prod_type_desc']; ?></td>
                                        <td align="center">
                                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal"
                                                onclick="showDetail('<?php echo htmlspecialchars($row['prod_detail']); ?>', '<?php echo $row['prod_price']; ?>')"><i class="bi bi-eye"></i>

                                            </button>
                                            <?php
                                            // Determine if the button should be disabled
                                            $button_class = ($row['prod_status_desc'] === 'Active') ? 'btn btn-primary' : 'btn disabled btn-primary';
                                            ?>
                                            <!-- Button trigger for Add Product To Cart form modal -->
                                            <button type="button" class="<?php echo $button_class; ?>" data-bs-backdrop="false"
                                                data-bs-toggle="modal" data-bs-target="#modalAddCart"
                                                data-prod-id="<?php echo $row['prod_id']; ?>"
                                                <?php echo ($row['prod_status_desc'] !== 'Active') ? 'disabled' : ''; ?>>
                                                <span class="fas fa-cart-plus"></span>
                                            </button>

                                            <?php if ($_SESSION["user_level"] !== "U") : ?>
                                                <a href="edit_product.php?prod_id=<?php echo $row['prod_id']; ?>" class="btn btn-warning">
                                                    <span class="fas fa-edit"></span>
                                                </a>

                                                <button class="btn btn-danger" onclick="deleteProduct(<?php echo $row['prod_id']; ?>)">
                                                    <span class="fas fa-trash-alt"></span>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>


        <!-- ขยายรูปออกมาเป็น Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <img id="imageModalSrc" src="" class="img-fluid" alt="Expanded Image">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ขยายรูปออกมาเป็น Modal -->

        <!-- Add Product form Modal l -->
        <div class="modal fade text-left" id="modalAddProduct" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">New Product</h4>
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
                                        <input type="text" id="prod_name" placeholder="Enter Product Name"
                                            class="form-control">
                                        <div class="invalid-feedback" id="nameFeedback"></div>
                                    </div>
                                    <label>Amount: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount" min="0" oninput="validity.valid||(value='');" placeholder="Enter Product Amount"
                                            class="form-control">
                                        <div class="invalid-feedback" id="amountFeedback"></div>
                                    </div>
                                    <label>Amount Minimum: </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_amount_min" min="0" oninput="validity.valid||(value='');" placeholder="Enter Product Amount Minimum"
                                            class="form-control">
                                        <div class="invalid-feedback" id="amountMinFeedback"></div>
                                    </div>
                                    <label>Price(baht): </label>
                                    <div class="form-group">
                                        <input type="number" id="prod_price" min="0" oninput="validity.valid||(value='');" placeholder="Enter Product Price"
                                            class="form-control">
                                        <div class="invalid-feedback" id="priceFeedback"></div>
                                    </div>
                                    <label>Unit: </label>
                                    <div class="form-group">
                                        <input type="text" id="prod_unit" placeholder="Enter Product Unit"
                                            class="form-control">
                                        <div class="invalid-feedback" id="unitFeedback"></div>
                                    </div>
                                    <label>Product Type: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_type">
                                            <option value="" selected>Select Product Type</option> <!-- Default option -->
                                            <?php
                                            while ($type = $result_type->fetch_assoc()) :
                                            ?>
                                                <option value="<?php echo $type['prod_type_id']; ?>"><?php echo $type['prod_type_desc']; ?></option>
                                            <?php endwhile ?>
                                        </select>
                                        <div class="invalid-feedback" id="typeFeedback"></div>
                                    </div>
                                    <label>Product Status: </label>
                                    <div class="form-group">
                                        <select class="form-select" id="prod_status">
                                            <option value="" selected>Select Product Status</option> <!-- Default option -->
                                            <?php
                                            while ($status = $result_status->fetch_assoc()) :
                                            ?>
                                                <option value="<?php echo $status['prod_status']; ?>"><?php echo $status['prod_status_desc']; ?></option>
                                            <?php endwhile ?>
                                        </select>
                                        <div class="invalid-feedback" id="statusFeedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Detail:</label>
                                    <div class="form-group">
                                        <textarea class="form-control" id="prod_detail" placeholder="Please enter detail" rows="3"></textarea>
                                        <div class="invalid-feedback" id="detailFeedback"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="formFile" class="form-label"> Photo </label>
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
                                        <span class="d-none d-sm-block">Add multiple products</span>
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Cancle</span>
                                    </button>
                                    <button type="button" class="btn btn-success ml-1" onclick="addProduct()">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Product form Modal -->


        <!-- Add Cart form Modal -->
        <div class="modal fade text-left" id="modalAddCart" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="myModalLabel33">Add Product To Cart</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <!-- prod_id ที่จะส่งไปตะกร้า -->
                                    <input hidden readonly id="cart_id" class="form-control" type="text">

                                    <label>Name: </label>
                                    <div class="form-group">
                                        <input type="text" id="cart_name" class="form-control" placeholder="ชื่อสินค้า" readonly>
                                    </div>
                                    <label>Amount: </label>
                                    <div class="form-group">
                                        <input type="number" id="cart_amount" min="1" oninput="validity.valid||(value='');"
                                            placeholder="Enter Product Amount" class="form-control" required>
                                        <div class="invalid-feedback" id="amountCartFeedback"></div>
                                    </div>
                                    <label>Unit: </label>
                                    <div class="form-group">
                                        <input type="text" id="cart_unit" class="form-control" placeholder="หน่วยสินค้า" readonly>
                                    </div>
                                    <label>Detail:</label>
                                    <div class="form-group">
                                        <input type="text" id="cart_detail" class="form-control" placeholder="Enter Detail">
                                        <div class="invalid-feedback" id="detailFeedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center">
                                    <div class="form-group">
                                        <img id="cart_img" src="../photo/no_img.jpg" alt="Product Image"
                                            style="display:block; margin-top:10px; max-width: 100%; height: auto;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Cancle</span>
                            </button>
                            <button type="button" class="btn btn-success ml-1" onclick="addCart()">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Add To Cart</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add cart form Modal -->

        <!-- Modal Detail-->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white" id="detailModalLabel">Product Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Detail:</strong> <span id="detailContent"></span></p>
                        <p><strong>Price:</strong> <span id="priceContent"></span> baht</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Detail-->


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
        //ฟังชันลบสินค้า
        function deleteProduct(prod_id) {
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
                                    text: result.status,
                                    timer: 1000,
                                    showConfirmButton: false
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

        //ฟังชันเพิ่มสินค้า
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
            if ($('#prod_detail').val() == "") {
                $('#prod_detail').addClass('is-invalid');
                $('#detailFeedback').text("detail is empty.");
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
                formData.append('detail', $('#prod_detail').val());
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
                                title: 'Add product success!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Wrong product added!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                // Validation failed, show an error message and keep the modal open
                Swal.fire({
                    title: "Wrong product added!",
                    text: "Please fill in all information completely.",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
            }

            if (!fileUploaded && isValid) {
                // Close the modal if no file was uploaded and the form is valid
                $('#modalAddProduct').modal('hide');
            }
        }

        // ดึงข้อมูลเพื่อจะเพิ่มสินค้าลงตะกร้า 
        // Event listener for the modal when it's triggered
        $('#modalAddCart').on('show.bs.modal', function(event) {
            // Get the button that triggered the modal
            var button = $(event.relatedTarget);
            // Extract the prod_id from the data-* attribute
            var prod_id = button.data('prod-id');

            // Make an AJAX request to fetch the product details using prod_id
            $.ajax({
                url: '/Final_Project/api/api_modal_cart.php', // The PHP file to handle the request
                type: 'GET',
                data: {
                    prod_id: prod_id
                },
                success: function(response) {
                    try {
                        // Assuming the response is JSON data with product details
                        var product = JSON.parse(response);

                        // Populate the modal fields with the product details
                        $('#cart_id').val(product.prod_id);
                        $('#cart_name').val(product.prod_name);
                        $('#cart_unit').val(product.prod_unit);

                        // If the image is defined, set it, otherwise use a fallback
                        if (product.prod_img && product.prod_img !== '') {
                            $('#cart_img').attr('src', '../photo/' + product.prod_img);
                        } else {
                            $('#cart_img').attr('src', '../photo/no_img.jpg');
                        }
                    } catch (error) {
                        console.error('Error parsing product details:', error);
                    }
                },
                error: function() {
                    console.error('Error loading product details');
                }
            });
        });

        //ฟังชันเอาสินค้าเข้าตะกร้า
        function addCart() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Form validation checks
            if ($('#cart_amount').val() == "") {
                $('#cart_amount').addClass('is-invalid');
                $('#amountCartFeedback').text("Amount is empty.");
                isValid = false;
            }
            if ($('#cart_detail').val() == "") {
                $('#cart_detail').addClass('is-invalid');
                $('#detailFeedback').text("Detail is empty.");
                isValid = false;
            }

            if (isValid) {
                let formData = new FormData();
                formData.append('id', $('#cart_id').val());
                formData.append('amount', $('#cart_amount').val());
                formData.append('detail', $('#cart_detail').val());

                $.ajax({
                    url: "/Final_Project/api/api_add_cart_hr.php",
                    type: 'POST',
                    dataType: "json",
                    data: formData,
                    processData: false, // Prevent jQuery from converting the FormData object into a query string
                    contentType: false, // Prevent jQuery from overriding the content type
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Product added to cart successfully!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Wrong item added to cart!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                // Validation failed, show an error message and keep the modal open
                Swal.fire({
                    title: "Wrong item added to cart!",
                    text: "Please fill in all information completely.",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
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
                imagePreview.src = '../photo/no_img.jpg'; // Change this to the appropriate path
            }
        });

        // ไปหน้า add_product.php
        function redirectToAddProduct() {
            window.location.href = 'add_product.php';
        }

        //ขยายรูปออกมา
        function expandImage(imageSrc) {
            // Set the image source in the modal
            $('#imageModalSrc').attr('src', '../photo/' + imageSrc);
            // Show the modal
            $('#imageModal').modal('show');
        }

        //นับจำนวนสินค้าที่อยู่ในรถเข็นขึ้น show ที่ปุ่ม
        $(document).ready(function() {
            // Fetch the cart count on page load
            updateCartCount();

            function updateCartCount() {
                $.ajax({
                    url: '/Final_Project/api/api_cart_count_hr.php', // Adjust the path as needed
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === "success") {
                            $('#cart_count').text(response.total_items);
                        } else {
                            $('#cart_count').text(0); // Fallback if something goes wrong
                        }
                    },
                    error: function() {
                        $('#cart_count').text(0); // Fallback in case of error
                    }
                });
            }
        });

        function showDetail(detail, price) {
            // แสดงรายละเอียดสินค้า
            document.getElementById('detailContent').textContent = detail;
            // แสดงราคา
            document.getElementById('priceContent').textContent = price;
        }
    </script>




    </body>

    </html>

<?php
} else {
    header("location: ../user/error_user_page.php");
}
?>