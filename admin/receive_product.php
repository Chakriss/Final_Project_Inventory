<?php
session_start();
// include_once 'config/connect_db.php';
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

    $stock = $_SESSION["user_stock"];
    $result = selectProduct($conn, $stock);

    $productData = [];
    while ($product = $result->fetch_assoc()) {
        $productData[] = $product;
    }

?>

    <script>
        let productData = <?php echo json_encode($productData); ?>;
    </script>
    <style>
        .card-header {
            background: linear-gradient(135deg, #6a82fb, #fc5c7d);
            /* Gradient background */
            border-bottom: 2px solid #ddd;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
            color: #fff;
            /* White text color to contrast with the gradient background */
        }

        .card-header h3 {
            margin: 0;
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff;
            /* Ensure the header text remains white */
        }

        .card-header label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            /* Ensure the label text remains white */
        }
    </style>

    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Receive The Product</h3>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row" id="table-bordered">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="receive_date">Receive Date: <span class="required">*</span></label>
                                        <input type="date" id="receive_date" class="form-control">
                                        <div class="invalid-feedback" id="dateFeedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="receive_time">Receive Time: <span class="required">*</span></label>
                                        <input type="time" id="receive_time" class="form-control">
                                        <div class="invalid-feedback" id="timeFeedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="productTable">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Photo</th>
                                                <th style="text-align: center;">Name</th>
                                                <th style="text-align: center;">Quantity</th>
                                                <th style="text-align: center;">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td align="center">
                                                    <!-- Placeholder icon -->
                                                    <i class="bi bi-image" id="product_icon_0" style="font-size: 48px;"></i>
                                                    <img src="../photo/no_img.jpg" id="product_image_0" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10%; display: none;" onclick="expandImage('no_img.jpg')">
                                                </td>
                                                <td>
                                                    <!-- Dropdown for product selection -->
                                                    <select class="form-select" name="products[0][product_id]" onchange="loadProductImage(this)">
                                                        <option value="" selected>Select Product</option>
                                                        <?php foreach ($productData as $product) : ?>
                                                            <option value="<?php echo $product['prod_id']; ?>"><?php echo $product['prod_name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="productFeedback_0"></div>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" id="amount" name="products[0][amount]" min="1" oninput="validity.valid||(value='');" placeholder="Enter Quantity / กรุณากรอกจำนวน">
                                                    <div class="invalid-feedback" id="amountFeedback_0"></div>
                                                </td>
                                                <td align="center">
                                                    <button type="button" class="btn btn-danger" onclick="removeProduct(this)">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <button type="button" class="btn btn-primary mt-3" onclick="addProductRow()">Add Form Product</button>
                                </div>
                                <div>
                                    <button class="btn btn-success mt-3 ms-3" onclick="comfirmProduct()">
                                        <span class="fas fa-check"></span> Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
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

    <?php
    include_once '../footer.php';
    ?>

    <script>
        let productIndex = 1;

        // Function to add a new product row
        function addProductRow() {
            const table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');

            // Build the dropdown options using the productData array
            let options = '<option value="" selected>Select Product</option>';
            productData.forEach(product => {
                options += `<option value="${product.prod_id}">${product.prod_name}</option>`;
            });

            newRow.innerHTML = `
        <td align="center">
            <i class="bi bi-image" id="product_icon_${productIndex}" style="font-size: 48px;"></i>
            <img src="../photo/no_img.jpg" id="product_image_${productIndex}" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10%; display: none;" onclick="expandImage('no_img.jpg')">
        </td>
        <td>
            <select class="form-select" name="products[${productIndex}][product_id]" onchange="loadProductImage(this)">
                ${options}
            </select>
            <div class="invalid-feedback" id="productFeedback_${productIndex}"></div>
        </td>
        <td>
            <input type="number" class="form-control" name="products[${productIndex}][amount]" min="1" oninput="validity.valid||(value='');" placeholder="Enter Quantity / กรุณากรอกจำนวน">
            <div class="invalid-feedback" id="productFeedback_${productIndex}"></div>
        </td>
        <td align="center">
            <button type="button" class="btn btn-danger" onclick="removeProduct(this)">Remove</button>
        </td>
    `;

            table.appendChild(newRow);
            productIndex++;
        }


        // Function to load the product image after selecting a product
        function loadProductImage(selectElement) {
            let productId = selectElement.value;
            let row = selectElement.closest('tr');
            let imgElement = row.querySelector('img');
            let iconElement = row.querySelector('i');

            // Example AJAX request to fetch product image
            $.ajax({
                url: '/Final_Project/api/fetch_product_image.php',
                type: 'POST',
                data: {
                    prod_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Hide icon and show image
                        iconElement.style.display = 'none';
                        imgElement.style.display = 'block';
                        imgElement.src = '../photo/' + response.prod_img;
                        imgElement.setAttribute('onclick', `expandImage('${response.prod_img}')`);
                    } else {
                        // If no image found, show default no_img.jpg and hide icon
                        iconElement.style.display = 'none';
                        imgElement.style.display = 'block';
                        imgElement.src = 'photo/no_img.jpg';
                        imgElement.setAttribute('onclick', "expandImage('no_img.jpg')");
                    }
                }
            });
        }

        // Function to expand the image in a modal
        function expandImage(imageSrc) {
            // Set the image source in the modal
            $('#imageModalSrc').attr('src', '../photo/' + imageSrc);
            // Show the modal
            $('#imageModal').modal('show');
        }

        // Function to remove a product row
        function removeProduct(button) {
            let row = button.closest('tr');
            row.remove();
        }



        function comfirmProduct() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            if ($('#receive_date').val() == "") {
                $('#receive_date').addClass('is-invalid');
                $('#dateFeedback').text("date is empty.");
                isValid = false;
            }
            if ($('#receive_time').val() == "") {
                $('#receive_time').addClass('is-invalid');
                $('#timeFeedback').text("time is empty.");
                isValid = false;
            }
            // Gather all product data
            let products = [];
            $('#productTable tbody tr').each(function(index, row) {
                let productId = $(row).find('select[name^="products"]').val();
                let amount = $(row).find('input[name^="products"]').val();

                if (!productId) {
                    isValid = false;
                    $(row).find('.form-select').addClass('is-invalid');
                    $(row).find('.invalid-feedback').text('Please select a product.');
                }

                if (!amount || amount < 1) {
                    isValid = false;
                    $(row).find('input[name^="products"]').addClass('is-invalid');
                    $(row).find('.invalid-feedback').text('Please enter a valid Quantity.');
                }

                if (productId && amount) {
                    products.push({
                        product_id: productId,
                        amount: amount
                    });
                }
            });

            // If the form is valid, proceed to submit the data
            if (isValid) {
                // Send product data to the server
                $.ajax({
                    url: '/Final_Project/api/api_receive_product.php',
                    type: 'POST',
                    data: {
                        products: products,
                        date: $('#receive_date').val(),
                        time: $('#receive_time').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Products have been successfully received!',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                // Optionally reload the page or redirect to another page
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Something went wrong!'
                            });
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Form',
                    text: 'Please correct the errors in the form.'
                });
            }
        }

        //ดึงวันที่และเวลา
    $(document).ready(function() {
        // Set the current date
        var today = new Date().toISOString().split('T')[0];
        $('#receive_date').val(today);

        // Set the current time
        var now = new Date();
        var hours = ('0' + now.getHours()).slice(-2);
        var minutes = ('0' + now.getMinutes()).slice(-2);
        $('#receive_time').val(hours + ':' + minutes);
    });
    </script>

<?php
} else {
    header("location: ../admin/error_admin_page.php");
}
?>