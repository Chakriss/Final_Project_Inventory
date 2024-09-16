<?php
$user_stock = $_SESSION["user_stock"];


if ($user_stock == 1) {
    $stock = 'stock_it.php';
} else {
    $stock = 'stock_hr.php';
}

?>
<style>
    .active>a {
        color: #0d6efd;
    }

    .d-block {
        display: block;
    }

    .submenu {
        display: none;
    }
</style>


</head>


<body>
    <div id="app">
        <!-- Sidebar -->
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="admin_page.php"><img src="assets/images/logo/logo_optinova.png" alt="Logo" style="width: 200px; height: auto;" srcset=""></a>
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
                            <a href="admin_page.php" class='sidebar-link'> <i class="bi bi-grid-fill"></i> <span>Dashboard</span> </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="<?php echo $stock ?>" class='sidebar-link'> <i class="bi bi-database"></i> <span>Product <?php echo $user_stock == 1 ? 'IT' : 'HR'; ?></span> </a>
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
                            <a href="withdraw.php" class='sidebar-link'> <i class="bi bi-cart-check-fill"></i></i> <span>Withdraw</span> <span id="withdraw_count"></span></a>
                        </li>


                        <!-- Add other menu items based on user permissions -->
                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clock-history"></i>
                                <span>History</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
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

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clipboard2-fill"></i>
                                <span>Report</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="report_product_min.php"><span> Products Low</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="report_popular_product.php"><span> Popular Products</a>
                                </li>

                                <li class="submenu-item">
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


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarItems = document.querySelectorAll('.sidebar-item');
                const currentUrl = window.location.href;

                sidebarItems.forEach(item => {
                    const link = item.querySelector('a').href;

                    if (link === currentUrl) {
                        item.classList.add('active');
                        if (item.classList.contains('has-sub')) {
                            const submenu = item.querySelector('.submenu');
                            if (submenu) {
                                submenu.classList.add('d-block');
                            }
                        }
                    } else {
                        if (item.classList.contains('has-sub')) {
                            const submenu = item.querySelector('.submenu');
                            if (submenu) {
                                submenu.classList.remove('d-block');
                            }
                        }
                    }

                    item.addEventListener('click', function() {
                        // Remove active class from all sidebar items and submenu items
                        sidebarItems.forEach(el => {
                            el.classList.remove('active');
                            const submenu = el.querySelector('.submenu');
                            if (submenu) {
                                submenu.classList.remove('d-block');
                            }
                        });

                        // Add active class to the clicked sidebar item
                        this.classList.add('active');

                        if (this.classList.contains('has-sub')) {
                            const submenu = this.querySelector('.submenu');
                            if (submenu) {
                                submenu.classList.toggle('d-block');
                            }
                        }
                    });
                });

                // Handle submenu item click
                const submenuItems = document.querySelectorAll('.submenu-item');
                submenuItems.forEach(subItem => {
                    subItem.addEventListener('click', function(event) {
                        event.stopPropagation();

                        // Remove active class from all sidebar items
                        sidebarItems.forEach(item => item.classList.remove('active'));

                        // Add active class to the parent sidebar item
                        const parentSidebarItem = this.closest('.sidebar-item.has-sub');
                        if (parentSidebarItem) {
                            parentSidebarItem.classList.add('active');
                        }

                        // Ensure submenu is visible
                        if (parentSidebarItem) {
                            const submenu = parentSidebarItem.querySelector('.submenu');
                            if (submenu) {
                                submenu.classList.add('d-block');
                            }
                        }
                    });
                });

                function scrollToActiveSidebarItem() {
                    const activeSidebarItem = document.querySelector('.sidebar-item.active');
                    // console.log('Active sidebar item:', activeSidebarItem); // Debugging statement
                    if (activeSidebarItem) {
                        activeSidebarItem.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }

                // Call function to handle initial state
                scrollToActiveSidebarItem();
            });

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
                            // console.log(response); // ตรวจสอบว่าข้อมูลมาถูกต้อง
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