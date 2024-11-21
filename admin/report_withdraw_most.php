<?php

session_start();
include_once '../config/function.php';
include_once '../header.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}
// Check the user level and include relevant files
if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "A") {
} else {
    // Redirect or show an error if the user level is not "User"
    header("Location: ../admin/error_admin_page.php");
    exit();
}

$stock = $_SESSION["user_stock"];

// Set default month and year to current month and year
$currentMonth = date('m');
$currentYear = date('Y');
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : $currentMonth;
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

$result_withdraw_most_product = selectWithdrawMostProduct($conn, $stock, $selectedMonth, $selectedYear);
$result_product_dept = selectProductDeptByDept($conn, $stock, $selectedMonth, $selectedYear);

// Prepare data for the chart
$dept_names = [];
$product_amounts = [];

if ($result_withdraw_most_product->num_rows > 0) {
    while ($row = $result_withdraw_most_product->fetch_assoc()) {
        $dept_names[] = $row['dept_name'];
        $product_amounts[] = (int)$row['prod_amount'];
    }
} else {
    //echo "No data found";
}

// สร้าง array เก็บข้อมูลของแผนกแต่ละแผนก
$departments = [];
while ($row = $result_product_dept->fetch_assoc()) {
    $departments[$row['dept_name']][] = $row;
}

$user_stock = $_SESSION["user_stock"];


if ($user_stock == 1) {
    $stock_menu = 'stock_it.php';
} else {
    $stock_menu = 'stock_hr.php';
}
?>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="admin_page.php"><img src="../assets/images/logo/logo_optinova.png" alt="Logo" style="width: 200px; height: auto;" srcset=""></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item">
                            <a href="admin_page.php" class='sidebar-link'> <i class="bi bi-grid-fill"></i> <span>Home</span> </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="<?php echo $stock_menu ?>" class='sidebar-link'> <i class="bi bi-database"></i> <span>Product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="receive_product.php" class='sidebar-link'> <i class="bi bi-database-add"></i></i> <span>Receive the product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Product Detail</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="product_status.php"><span>Product Status <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                </li>

                                <li class="submenu-item">
                                    <a href="product_type.php"><span>Product Type <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="withdraw.php" class='sidebar-link'> <i class="bi bi-cart-check-fill"></i> </i> <span>Withdraw</span> <span id="withdraw_count"></span></a>
                        </li>

                        <!-- Add other menu items based on user permissions -->
                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clock-history"></i>
                                <span>History</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="History_withdraw.php"><span>Withdraw <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                </li>

                                <li class="submenu-item">
                                    <a href="History_receive.php"><span>Receive <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Account</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="account_set.php"><span> Users</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="department.php"><span> department</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clipboard2-fill"></i>
                                <span>Report</span>
                            </a>
                            <ul class="submenu active">
                                <li class="submenu-item">
                                    <a href="report_product_min.php"><span> Products Low</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_popular_product.php"><span> Popular Products</a>
                                </li>

                                <li class="submenu-item active">
                                    <a href="report_withdraw_most.php"><span> Department Withdraws Most Products</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_totalprice_most.php"><span> Department Total Price Most Products</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <!-- End Sidebar -->

        <?php
        include_once '../navbar.php';
        ?>

        <div id="main">
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 order-md-1 order-last">
                            <h3>Top 10 departments that withdraw the most products</h3>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header" style="margin-bottom: 20px;">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="month">Select Month:</label>
                                        <select id="month" name="month" class="form-control">
                                            <?php
                                            for ($m = 1; $m <= 12; $m++) {
                                                $monthValue = sprintf('%02d', $m);
                                                $selected = ($monthValue == $selectedMonth) ? 'selected' : '';
                                                echo "<option value='$monthValue' $selected>" . date('F', mktime(0, 0, 0, $m, 10)) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="year">Select Year:</label>
                                        <select id="year" name="year" class="form-control">
                                            <?php
                                            for ($y = 2020; $y <= date('Y'); $y++) {
                                                $selected = ($y == $selectedYear) ? 'selected' : '';
                                                echo "<option value='$y' $selected>$y</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label><br>
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div id="chart"></div>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h3>Detail </h3>
                    <?php foreach ($departments as $dept_name => $products) { ?>
                        <div class="card">
                            <div class="card-header">
                                <h4><?php echo $dept_name; ?></h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product) { ?>
                                            <tr>
                                                <td><?php echo $product['prod_name']; ?></td>
                                                <td><?php echo $product['prod_amount']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </section>

            </div>

            <script>
                // PHP arrays passed to JavaScript
                var productNames = <?php echo json_encode($dept_names); ?>;
                var productAmounts = <?php echo json_encode($product_amounts); ?>;

                // Create the chart options
                var options = {
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    series: [{
                        name: 'Product Quantity',
                        data: productAmounts
                    }],
                    xaxis: {
                        categories: productNames // Display product names on the X-axis
                    },
                    colors: ['#008FFB'], // Define the color of the bars
                    legend: {
                        show: true // Show legend
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false, // Vertical bars
                            columnWidth: '55%', // Width of bars
                        }
                    }
                };

                // Render the chart
                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();



                //นับจำนวนสินค้าที่อยู่ในรถเข็นขึ้น show ที่ปุ่ม
                $(document).ready(function() {
                    // ดึงค่า withdraw_count จาก Local Storage ถ้ามี
                    let savedWithdrawCount = localStorage.getItem('withdraw_count');
                    if (savedWithdrawCount !== null) {
                        $('#withdraw_count').text(savedWithdrawCount);
                    }

                    // Fetch the cart count on page load
                    updateCartCount();

                    function updateCartCount() {
                        $.ajax({
                            url: '/Final_Project/api/api_withdraw_count.php', // Adjust the path as needed
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                console.log(response); // ตรวจสอบว่าข้อมูลมาถูกต้อง
                                if (response.status === "success") {
                                    console.log("Updating withdraw_count to:", response.total_items);
                                    $('#withdraw_count').text(response.total_items);

                                    // เก็บค่าใน Local Storage
                                    localStorage.setItem('withdraw_count', response.total_items);
                                } else {
                                    console.log("API status is not 'success'");
                                    $('#withdraw_count').text(0); // Fallback if something goes wrong
                                    localStorage.setItem('withdraw_count', 0);
                                }
                            },
                            error: function() {
                                $('#withdraw_count').text(0); // Fallback in case of error
                                localStorage.setItem('withdraw_count', 0);
                            }
                        });
                    }
                });
            </script>

            <?php
            include_once '../footer.php';
            ?>