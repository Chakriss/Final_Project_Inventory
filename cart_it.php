<?php
session_start();
include_once 'config/function.php';
// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

//เรียกใช้ฟังชันดึงข้อมูลใน cart
$cart_data = cartDetail($conn);
$max_cart_id = $cart_data['max_cart_id'];
$cart_result = $cart_data['cart_result'];

//เรียกใช้ฟังชันดึงแผนก
$result_dept = selectDept($conn);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
<style>
    .quantity-button {
        width: 40px;
        height: 40px;
        font-size: 20px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        margin: 0 15px;
        color: white;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .quantity-button:focus {
        outline: none;
    }

    .btn-decrease {
        background-color: #e74c3c;
        /* สีแดง */
    }

    .btn-decrease:hover {
        background-color: #c0392b;
    }

    .btn-increase {
        background-color: #2ecc71;
        /* สีเขียว */
    }

    .btn-increase:hover {
        background-color: #27ae60;
    }

    .custom-select-width {
        width: 200px;
        /* Adjust the width as needed */
    }
</style>


<div id="main">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cart</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <label>Department: <span class="required">*</span></label>
                    <div class="form-group">
                        <select class="form-select" id="cart_dept" style="width: 200px;"> <!-- Adjust the width as needed -->
                            <option value="" selected>Select Department</option> <!-- Default option -->
                            <?php
                            while ($dept = $result_dept->fetch_assoc()) :
                            ?>
                                <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                            <?php endwhile ?>
                        </select>
                        <div class="invalid-feedback" id="deptFeedback"></div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover" id="table1">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Name</th>
                                <th style="text-align: center;">Amount</th>
                                <th style="text-align: center;">Detail</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $cart_result->fetch_assoc()) : ?>
                                <tr id="row_<?php echo $row['cart_detail_id']; ?>">
                                    <!-- prod_id ที่จะส่งไปตะกร้า -->
                                    <input hidden readonly id="cart_id" class="form-control" type="text">
                                    <td align="center"><?php echo $row['prod_name']; ?></td>
                                    <td align="center">
                                        <button class="quantity-button btn-decrease" onclick="decreaseQuantity(<?php echo $row['cart_detail_id']; ?>)">-</button>
                                        <span id="quantity-<?php echo $row['cart_detail_id']; ?>"><?php echo $row['cart_amount']; ?></span>
                                        <button class="quantity-button btn-increase" onclick="increaseQuantity(<?php echo $row['cart_detail_id']; ?>)">+</button>
                                    </td>
                                    <td align="center"><?php echo $row['cart_detail']; ?></td>
                                    <td align="center">
                                        <span class="badge bg-warning"><?php echo $row['cart_status']; ?></span>
                                    </td>
                                    <td align="center">
                                        <button style="border-radius: 50%;" class="btn btn-danger" onclick="deleteCart(<?php echo $row['cart_detail_id']; ?>)">
                                            <span class="fas fa-eraser"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a class="btn btn-primary me-2" href="stock_it.php">
                    Back to Stock
                </a>
                <button class="btn btn-success" onclick="comfirmCart()">
                    <span class="fas fa-check"></span> Confirm
                </button>
            </div>

        </section>
    </div>




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
    function deleteCart(cart_detail_id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/Final_Project/api/api_delete_cart.php",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        cart_detail_id: cart_detail_id
                    },
                    success: function(result) {
                        if (result.color === "success") {
                            // Remove the row from the table
                            $('#row_' + cart_detail_id).remove();
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

    function increaseQuantity(cart_detail_id) {
        let quantityElement = document.getElementById('quantity-' + cart_detail_id);
        let currentQuantity = parseInt(quantityElement.textContent);

        // เพิ่มจำนวน
        quantityElement.textContent = currentQuantity + 1;

        // ทำการอัพเดตจำนวนในฐานข้อมูลผ่าน AJAX
        updateCartQuantity(cart_detail_id, currentQuantity + 1);
    }

    function decreaseQuantity(cart_detail_id) {
        let quantityElement = document.getElementById('quantity-' + cart_detail_id);
        let currentQuantity = parseInt(quantityElement.textContent);

        if (currentQuantity > 1) {
            // ลดจำนวน
            quantityElement.textContent = currentQuantity - 1;

            // ทำการอัพเดตจำนวนในฐานข้อมูลผ่าน AJAX
            updateCartQuantity(cart_detail_id, currentQuantity - 1);
        }
    }

    function updateCartQuantity(cart_detail_id, newQuantity) {
        $.ajax({
            url: '/Final_Project/api/api_update_cart.php',
            type: 'POST',
            dataType: 'json', // เพิ่มการระบุประเภทข้อมูลที่คาดหวังจากเซิร์ฟเวอร์
            data: {
                cart_detail_id: cart_detail_id,
                cart_amount: newQuantity
            },
            success: function(result) {
                if (result.status === "successfully") {
                    // เพิ่มการจัดการกรณีที่การอัพเดตสำเร็จ เช่น การรีเฟรชข้อมูล
                } else {
                    Swal.fire({
                        title: "Add amount fail!",
                        text: result.message,
                        icon: "error"
                    });
                }
            }
        });
    }



    function comfirmCart() {
        event.preventDefault();
        let cart_id = "<?php echo $max_cart_id; ?>";
        let isValid = true;

        // Reset validation messages
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');

        if ($('#cart_dept').val() == "") {
            $('#cart_dept').addClass('is-invalid');
            $('#deptFeedback').text("Department is empty.");
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: "/Final_Project/api/api_confirm_cart_it.php",
                type: 'POST',
                dataType: "json",
                data: {
                    code: "xxx",
                    cart_id: cart_id,
                    dept: $('#cart_dept').val()
                },
                success: function(result) {
                    if (result.status === "successfully") {
                        Swal.fire({
                            title: 'Confirm cart successfully!',
                            icon: 'success'
                        }).then(() => {
                            window.location.href="stock_it.php";
                        });
                    } else {
                        Swal.fire({
                            title: "Confirm cart fail!",
                            text: result.message,
                            icon: "error"
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                title: "Wrong confirm cart!",
                text: "Please fill in the department.",
                icon: "error"
            });
            return; // Keep modal open if validation fails
        }
    }
</script>


</body>

</html>