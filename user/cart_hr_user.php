<?php
session_start();
include_once '../config/function.php';
// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}

if (isset($_SESSION["user_level"]) && ($_SESSION["user_level"] == 'U')) {
    //เรียกใช้ฟังชันดึงข้อมูลใน cart
    $cart_data = cartDetailHr($conn);
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
    include_once 'menu_user.php';
    include_once '../navbar.php';

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

        .form-select-t {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 1rem;
            background-color: #fff;
            color: #333;
        }

        
    /* สไตล์สำหรับ textarea */
    textarea {
        resize: none; /* ปิดการปรับขนาด textarea */
        border: 1px solid #ccc; /* กำหนดเส้นขอบ */
        border-radius: 8px; /* ปรับให้ขอบโค้งมน */
        padding: 8px; /* เพิ่มระยะห่างภายใน */
        font-size: 14px; /* ปรับขนาดตัวอักษร */
        width: 100%; /* ให้ขยายเต็มพื้นที่ cell */
        background-color: #f9f9f9; /* พื้นหลังอ่อนเพื่อให้ดูสบายตา */
    }

    /* กำหนดขนาดพื้นที่ของ textarea ให้อยู่ในตารางอย่างสมดุล */
    .table textarea {
        max-height: 60px; /* กำหนดความสูงสูงสุด */
        min-height: 40px; /* กำหนดความสูงต่ำสุด */
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
                        <div class="row">
                            <!-- Department-->
                            <input hidden readonly id="cart_dept" class="form-control" type="text" value="<?php echo isset($_SESSION['user_dept']) ? $_SESSION['user_dept'] : ''; ?>">

                            <!-- Date Input -->
                            <div class="col-md-4">
                                <label for="cart_date">Date: <span class="required">*</span></label>
                                <input type="date" id="cart_date" class="form-control">
                                <div class="invalid-feedback" id="dateFeedback"></div>
                            </div>

                            <!-- Time Input -->
                            <div class="col-md-4">
                                <label for="cart_time">Time: <span class="required">*</span></label>
                                <input type="time" id="cart_time" class="form-control">
                                <div class="invalid-feedback" id="timeFeedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Product ID</th>
                                    <th style="text-align: center;">Name</th>
                                    <th style="text-align: center;">Total Quantity</th>
                                    <th style="text-align: center;">Quantity</th>
                                    <th style="text-align: center;">Detail</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $cart_result->fetch_assoc()) : ?>
                                    <tr id="row_<?php echo $row['cart_detail_id']; ?>">
                                        <td align="center"><?php echo $row['prod_id']; ?></td>
                                        <td align="center"><?php echo $row['prod_name']; ?></td>
                                        <td align="center"><?php echo $row['prod_amount']; ?></td>
                                        <td align="center">
                                            <button class="quantity-button btn-decrease" onclick="decreaseQuantity(<?php echo $row['cart_detail_id']; ?>)">-</button>
                                            <span id="quantity-<?php echo $row['cart_detail_id']; ?>"><?php echo $row['cart_amount']; ?></span>
                                            <button class="quantity-button btn-increase" onclick="increaseQuantity(<?php echo $row['cart_detail_id']; ?>)">+</button>
                                        </td>
                                        <td align="center">
                                            <textarea rows="2" readonly><?php echo $row['cart_detail']; ?></textarea>
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
                    <a class="btn btn-primary me-2" href="stock_hr_user.php">
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
        function deleteCart(cart_detail_id) {
            event.preventDefault();
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
                            title: "Add Quantity fail!",
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

            if ($('#cart_date').val() == "") {
                $('#cart_date').addClass('is-invalid');
                $('#dateFeedback').text("date is empty.");
                isValid = false;
            }
            if ($('#cart_time').val() == "") {
                $('#cart_time').addClass('is-invalid');
                $('#timeFeedback').text("time is empty.");
                isValid = false;
            }

            if (isValid) {
                $.ajax({
                    url: "/Final_Project/api/api_confirm_cart.php",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        code: "xxx",
                        cart_id: cart_id,
                        dept: $('#cart_dept').val(),
                        date: $('#cart_date').val(),
                        time: $('#cart_time').val()
                    },
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: 'Confirm cart successfully!',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "stock_hr_user.php";
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
                    text: "Please fill in the blank information.",
                    icon: "error"
                });
                return; // Keep modal open if validation fails
            }
        }

        //ดึงวันที่และเวลา
        $(document).ready(function() {
            // Set the current date
            var today = new Date().toISOString().split('T')[0];
            $('#cart_date').val(today);

            // Set the current time
            var now = new Date();
            var hours = ('0' + now.getHours()).slice(-2);
            var minutes = ('0' + now.getMinutes()).slice(-2);
            $('#cart_time').val(hours + ':' + minutes);
        });
    </script>


    </body>

    </html>

<?php
} else {
    header("location: ../user/error_user_page.php");
}
?>