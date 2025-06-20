<?php
include_once 'connect_db.php';

//<----------------------------- ส่วนของ stock -------------------------------------------------->
//ฟังชันดึงข้อมูลของสินค้า
function selectProduct($conn, $stock)
{
    // SQL query to retrieve product data
    $sql = "SELECT 
            product.prod_id, 
            product.prod_name, 
            product.prod_amount, 
            product.prod_amount_min, 
            product.prod_price, 
            product.prod_unit,
            product.prod_img,
            product.prod_detail,
            product.prod_date,
            product_status.prod_status_desc,
            product_type.prod_type_desc 
        FROM product
        LEFT JOIN product_status ON product.prod_status = product_status.prod_status 
        LEFT JOIN product_type ON product.prod_type_id = product_type.prod_type_id
        WHERE product.st_id = ?";

    // Prepare the SQL statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the $stock variable to the statement
        mysqli_stmt_bind_param($stmt, "i", $stock);

        // Execute the SQL statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Return the result set
        return $result;
    } else {
        // Handle error
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
}

//ฟังชันดึงข้อมูลของสินค้าเพื่อแก้ไข
function editProduct($conn, $prod_id)
{
    $sql = "SELECT * FROM product WHERE prod_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $prod_id);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

//ฟังชันดึงข้อมูลของประเภทสินค้าเพื่อแก้ไข
function editType($conn, $prod_type_id)
{
    $sql = "SELECT * FROM product_type WHERE prod_type_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $prod_type_id);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

//ฟังชันดึงข้อมูลประเภทของสินค้า
function selectType($conn)
{
    $stock = $_SESSION['user_stock'];
    $sql_type = "SELECT prod_type_id, prod_type_desc
    FROM product_type 
    WHERE st_id = ?";
    $stmt = mysqli_prepare($conn, $sql_type);
    mysqli_stmt_bind_param($stmt, "i", $stock);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_type = $stmt->get_result();
    return $result_type;
}

//ฟังชันดึงข้อมูลสถานะของสินค้า
function selectStatus($conn)
{
    $sql_status = "SELECT * FROM product_status";
    $stmt = mysqli_prepare($conn, $sql_status);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_status = $stmt->get_result();
    return $result_status;
}

//ฟังชันดึงข้อมูลแผนก
function selectDept($conn)
{
    $sql_dept = "SELECT * FROM department";
    $stmt = mysqli_prepare($conn, $sql_dept);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_dept = $stmt->get_result();
    return $result_dept;
}

//ฟังชันดึงข้อมูลสต๊อก
function selectStock($conn)
{
    $sql_stock = "SELECT * FROM stock_main WHERE st_id IN (1, 2)";
    $stmt = mysqli_prepare($conn, $sql_stock);

    // Execute the query
    $stmt->execute();
    $result_stock = $stmt->get_result();
    return $result_stock;
}

//ฟังชันดึงข้อมูลสถานะของผู้ใช้
function selectStatusUser($conn)
{
    $sql_status_user = "SELECT * FROM user_status";
    $stmt = mysqli_prepare($conn, $sql_status_user);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_status_user = $stmt->get_result();
    return $result_status_user;
}



//<----------------------------- ส่วนของ stock -------------------------------------------------->


//<----------------------------- ส่วนของ cart -------------------------------------------------->
function cartDetailIt($conn)
{
    $us_id = $_SESSION['user_id'];
    $stock = 1;  // Assuming stock ID is fixed for the query
    $status = 'TBC';  // Assuming 'WC' is the status code for active carts

    // Step 1: Get the maximum cart_id
    $max_cart_sql = "SELECT MAX(cart_id) as max_cart_id FROM cart 
                     WHERE st_id = ? AND us_id = ? AND cart_status_id = ?";
    $max_cart_stmt = mysqli_prepare($conn, $max_cart_sql);
    mysqli_stmt_bind_param($max_cart_stmt, "iis", $stock, $us_id, $status);
    mysqli_stmt_execute($max_cart_stmt);
    mysqli_stmt_bind_result($max_cart_stmt, $max_cart_id);
    mysqli_stmt_fetch($max_cart_stmt);
    mysqli_stmt_close($max_cart_stmt);

    // Step 2: Fetch cart details based on max_cart_id
    $cart_sql = "SELECT cart_detail.cart_detail_id,
                        cart_detail.prod_id,
                        product.prod_name,
                        product.prod_amount, 
                        cart_detail.cart_amount, 
                        cart_detail.cart_detail, 
                        cart_status.cart_status, 
                        cart_detail.cart_status_id
                FROM cart_detail
                LEFT JOIN product ON cart_detail.prod_id = product.prod_id
                LEFT JOIN cart_status ON cart_detail.cart_status_id = cart_status.cart_status_id
                WHERE cart_detail.cart_id = ?";
    $cart_stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($cart_stmt, "i", $max_cart_id);
    mysqli_stmt_execute($cart_stmt);

    // Get the result
    $cart_result = mysqli_stmt_get_result($cart_stmt);

    // Return both max_cart_id and the result set
    return ['max_cart_id' => $max_cart_id, 'cart_result' => $cart_result];
}

function cartDetailHr($conn)
{
    $us_id = $_SESSION['user_id'];
    $stock = 2;  // Assuming stock ID is fixed for the query
    $status = 'TBC';  // Assuming 'WC' is the status code for active carts

    // Step 1: Get the maximum cart_id
    $max_cart_sql = "SELECT MAX(cart_id) as max_cart_id FROM cart 
                     WHERE st_id = ? AND us_id = ? AND cart_status_id = ?";
    $max_cart_stmt = mysqli_prepare($conn, $max_cart_sql);
    mysqli_stmt_bind_param($max_cart_stmt, "iis", $stock, $us_id, $status);
    mysqli_stmt_execute($max_cart_stmt);
    mysqli_stmt_bind_result($max_cart_stmt, $max_cart_id);
    mysqli_stmt_fetch($max_cart_stmt);
    mysqli_stmt_close($max_cart_stmt);

    // Step 2: Fetch cart details based on max_cart_id
    $cart_sql = "SELECT cart_detail.cart_detail_id,
                        cart_detail.prod_id,
                        product.prod_name,
                        product.prod_amount, 
                        cart_detail.cart_amount, 
                        cart_detail.cart_detail, 
                        cart_status.cart_status, 
                        cart_detail.cart_status_id
                FROM cart_detail
                LEFT JOIN product ON cart_detail.prod_id = product.prod_id
                LEFT JOIN cart_status ON cart_detail.cart_status_id = cart_status.cart_status_id
                WHERE cart_detail.cart_id = ?";
    $cart_stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($cart_stmt, "i", $max_cart_id);
    mysqli_stmt_execute($cart_stmt);

    // Get the result
    $cart_result = mysqli_stmt_get_result($cart_stmt);

    // Return both max_cart_id and the result set
    return ['max_cart_id' => $max_cart_id, 'cart_result' => $cart_result];
}


//<----------------------------- ส่วนของ cart -------------------------------------------------->


//<----------------------------- ส่วนของ withdraw -------------------------------------------------->

function orderHead($conn, $stock)
{
    $status = 'P';
    $order_sql = "SELECT cart.cart_id, 
                         user.us_name, 
                         department.dept_name, 
                         cart.cart_date, 
                         cart.cart_time, 
                         cart_status.cart_status
                         FROM cart 
                         LEFT JOIN user ON cart.us_id = user.us_id
                         LEFT JOIN department ON cart.dept_id = department.dept_id
                         LEFT JOIN cart_status ON cart.cart_status_id = cart_status.cart_status_id
                         WHERE cart.cart_status_id = ? AND cart.st_id = ?";

    $order_head_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_head_stmt, "si", $status, $stock);
    mysqli_stmt_execute($order_head_stmt);

    $result_order_head = $order_head_stmt->get_result();
    return $result_order_head;
}

//<----------------------------- ส่วนของ withdraw -------------------------------------------------->
//<----------------------------- ส่วนของ withdraw User -------------------------------------------------->
function orderHeadUser($conn, $us_id)
{
    $excluded_status = 'P';
    $order_user_sql = "SELECT cart.cart_id, 
                             department.dept_name, 
                             cart.cart_date, 
                             cart.cart_time, 
                             cart_status.cart_status
                      FROM cart 
                      LEFT JOIN department ON cart.dept_id = department.dept_id
                      LEFT JOIN cart_status ON cart.cart_status_id = cart_status.cart_status_id
                      WHERE cart.cart_status_id = ? AND cart.us_id = ?
                      ORDER BY cart.cart_date DESC, cart.cart_time DESC";

    $order_head_user_stmt = mysqli_prepare($conn, $order_user_sql);
    mysqli_stmt_bind_param($order_head_user_stmt, "si", $excluded_status, $us_id);
    mysqli_stmt_execute($order_head_user_stmt);

    $result_order_head_user = $order_head_user_stmt->get_result();
    return $result_order_head_user;
}


//<----------------------------- ส่วนของ withdraw User -------------------------------------------------->

//<----------------------------- ส่วนของ history Admin -------------------------------------------------->

//ประวัติออเดอร์
function orderHistory($conn, $stock)
{
    // Statuses to include
    $included_statuses = ['A', 'R'];

    // Convert the statuses to a string format for use in the SQL query
    $status_placeholders = implode(',', array_fill(0, count($included_statuses), '?'));

    $order_user_sql = "SELECT cart.cart_id, 
                             user.us_name, 
                             department.dept_name, 
                             cart.cart_date, 
                             cart.cart_time, 
                             cart_status.cart_status
                      FROM cart 
                      LEFT JOIN user ON cart.us_id = user.us_id
                      LEFT JOIN department ON cart.dept_id = department.dept_id
                      LEFT JOIN cart_status ON cart.cart_status_id = cart_status.cart_status_id
                      WHERE cart.cart_status_id IN ($status_placeholders) AND cart.st_id = ?
                      ORDER BY cart.cart_date DESC, cart.cart_time DESC";

    $order_history_stmt = mysqli_prepare($conn, $order_user_sql);

    // Bind the status parameters
    $status_params = array_merge($included_statuses, [$stock]);
    mysqli_stmt_bind_param($order_history_stmt, str_repeat('s', count($included_statuses)) . 'i', ...$status_params);

    mysqli_stmt_execute($order_history_stmt);

    $result_order_history = $order_history_stmt->get_result();
    return $result_order_history;
}


//ประวัติสินค้าเข้า
function receiveHistory($conn, $stock)
{
    $receive_sql = "SELECT receive_product.rec_id,
                           receive_product.rec_date,
                           receive_product.rec_time,
                           user.us_name 
                    FROM receive_product 
                    LEFT JOIN user ON receive_product.us_id = user.us_id
                    WHERE receive_product.st_id = ?
                    ORDER BY receive_product.rec_date DESC, receive_product.rec_time DESC";

    $receive_history_stmt = mysqli_prepare($conn, $receive_sql);
    mysqli_stmt_bind_param($receive_history_stmt, "i", $stock);
    // Execute คำสั่ง SQL
    $receive_history_stmt->execute();
    $result_receive = $receive_history_stmt->get_result();
    return $result_receive;
}

//<----------------------------- ส่วนของ history Admin -------------------------------------------------->



//<----------------------------- ส่วนของ history User -------------------------------------------------->
function orderHistoryUser($conn, $us_id)
{
    // Statuses to include
    $included_statuses = ['A', 'R'];

    // Convert the statuses to a string format for use in the SQL query
    $status_placeholders = implode(',', array_fill(0, count($included_statuses), '?'));

    $order_user_sql = "SELECT cart.cart_id, 
                             department.dept_name, 
                             cart.cart_date, 
                             cart.cart_time, 
                             cart_status.cart_status
                      FROM cart 
                      LEFT JOIN department ON cart.dept_id = department.dept_id
                      LEFT JOIN cart_status ON cart.cart_status_id = cart_status.cart_status_id
                      WHERE cart.cart_status_id IN ($status_placeholders) AND cart.us_id = ?
                      ORDER BY cart.cart_date DESC, cart.cart_time DESC";

    $order_head_user_stmt = mysqli_prepare($conn, $order_user_sql);

    // Bind the status parameters
    $status_params = array_merge($included_statuses, [$us_id]);
    mysqli_stmt_bind_param($order_head_user_stmt, str_repeat('s', count($included_statuses)) . 'i', ...$status_params);

    mysqli_stmt_execute($order_head_user_stmt);

    $result_order_head_user = $order_head_user_stmt->get_result();
    return $result_order_head_user;
}

//<----------------------------- ส่วนของ history User -------------------------------------------------->


//<----------------------------- ส่วนของ Admin -------------------------------------------------->
function selectAdmin($conn)
{
    $admin_sql = "SELECT 
                      user.us_id,
                      user.us_name,
                      user.us_email,
                      user_status.us_status_desc
                  FROM user 
                  LEFT JOIN user_status ON user.us_status_id = user_status.us_status_id  
                  WHERE user.us_level_id = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $admin_sql);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        // Handle error - could log or return false, based on your needs
        error_log("Failed to prepare the statement: " . mysqli_error($conn));
        return false;
    }

    // Bind the parameter for the 'Admin' level
    $admin_level = 'A';
    mysqli_stmt_bind_param($stmt, "s", $admin_level);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Fetch the result set
        $result_admin = $stmt->get_result();
        return $result_admin;
    } else {
        // Handle execution error - could log or return false
        error_log("Failed to execute the statement: " . mysqli_error($conn));
        return false;
    }
}

//ดึงข้อมูลadmin มาแก้ไข
function editAdmin($conn, $us_id)
{
    $sql = "SELECT * FROM user WHERE us_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $us_id);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Assuming us_password is base64 encoded
        $row['us_password'] = base64_decode($row['us_password']);
    }

    return $row;
}


//ฟังชันดึงข้อมูลสิทธิ์การเข้าถึง
function selectPermission($conn)
{
    $sql_permission = "SELECT * FROM user_permission";
    $stmt = mysqli_prepare($conn, $sql_permission);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_permission = $stmt->get_result();
    return $result_permission;
}


//<----------------------------- ส่วนของ Admin -------------------------------------------------->

//<----------------------------- ส่วนของ user -------------------------------------------------->
function selectUser($conn)
{
    $admin_sql = "SELECT 
                      user.us_id,
                      user.us_name,
                      user.us_email,
                      user_status.us_status_desc
                  FROM user 
                  LEFT JOIN user_status ON user.us_status_id = user_status.us_status_id  
                  WHERE user.us_level_id = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $admin_sql);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        // Handle error - could log or return false, based on your needs
        error_log("Failed to prepare the statement: " . mysqli_error($conn));
        return false;
    }

    // Bind the parameter for the 'Admin' level
    $user_level = 'U';
    mysqli_stmt_bind_param($stmt, "s", $user_level);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Fetch the result set
        $result_user = $stmt->get_result();
        return $result_user;
    } else {
        // Handle execution error - could log or return false
        error_log("Failed to execute the statement: " . mysqli_error($conn));
        return false;
    }
}


//<----------------------------- ส่วนของ user -------------------------------------------------->


//<----------------------------- ส่วนของ Report -------------------------------------------------->

//สินค้าเหลือน้อย
function selectLowStock($conn, $stock)
{
    $sql = "SELECT * FROM product WHERE st_id = ? AND prod_amount <= prod_amount_min";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stock);
    // Execute คำสั่ง SQL
    $stmt->execute();
    $result_low_stock = $stmt->get_result();
    return $result_low_stock;
}

//สินค้ายอดนิยม
function selectPopularProduct($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql = "SELECT cart_detail.prod_name, SUM(cart_detail.cart_amount) AS prod_amount
            FROM cart_detail
            LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
            WHERE cart.st_id = ? 
            AND cart_detail.cart_status_id = 'A'
            AND cart.cart_status_id = 'A'
            AND MONTH(cart.cart_date) = ?
            AND YEAR(cart.cart_date) = ?
            GROUP BY cart_detail.prod_id, cart_detail.prod_name
            ORDER BY SUM(cart_detail.cart_amount) DESC LIMIT 10";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "iss", $stock, $selectedMonth, $selectedYear);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result_popular_product = $stmt->get_result();

    return $result_popular_product;
}

//แผนกที่เบิกสินค้าเยอะที่สุด
function selectWithdrawMostProduct($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql = "SELECT department.dept_name, SUM(cart_detail.cart_amount) AS prod_amount
            FROM cart_detail
            LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
            LEFT JOIN department ON cart.dept_id = department.dept_id
            WHERE cart.st_id = ? 
            AND cart_detail.cart_status_id = 'A'
            AND cart.cart_status_id = 'A'
            AND MONTH(cart.cart_date) = ?
            AND YEAR(cart.cart_date) = ?
            GROUP BY department.dept_name
            ORDER BY SUM(cart_detail.cart_amount) DESC LIMIT 10";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "iss", $stock, $selectedMonth, $selectedYear);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result_withdraw_most_product = $stmt->get_result();

    return $result_withdraw_most_product;
}

// ดึงข้อมูลสินค้าแยกตามแผนก และเรียงตามยอดรวมการเบิกมากที่สุดขึ้นก่อน
function selectProductDeptByDept($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql = "SELECT department.dept_name, cart_detail.prod_name, SUM(cart_detail.cart_amount) AS prod_amount, MAX(dept_totals.total_amount) AS total_amount
            FROM cart_detail
            LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
            LEFT JOIN department ON cart.dept_id = department.dept_id
            LEFT JOIN (
                SELECT department.dept_id, SUM(cart_detail.cart_amount) AS total_amount
                FROM cart_detail
                LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
                LEFT JOIN department ON cart.dept_id = department.dept_id
                WHERE cart.st_id = ?
                AND cart_detail.cart_status_id = 'A'
                AND cart.cart_status_id = 'A'
                AND MONTH(cart.cart_date) = ?
                AND YEAR(cart.cart_date) = ?
                GROUP BY department.dept_id
            ) AS dept_totals ON department.dept_id = dept_totals.dept_id
            WHERE cart.st_id = ?
            AND cart_detail.cart_status_id = 'A'
            AND cart.cart_status_id = 'A'
            AND MONTH(cart.cart_date) = ?
            AND YEAR(cart.cart_date) = ?
            GROUP BY department.dept_name, cart_detail.prod_name
            ORDER BY total_amount DESC, prod_amount DESC
            LIMIT 10";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $stock, $selectedMonth, $selectedYear, $stock, $selectedMonth, $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}


//แผนกที่เบิกสินค้าที่ราคาเยอะที่สุด
function selectTotalPriceMost($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql = "SELECT department.dept_name, SUM(cart_detail.cart_amount * cart_detail.prod_price) AS total_value
            FROM cart_detail
            LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
            LEFT JOIN department ON cart.dept_id = department.dept_id
            WHERE cart.st_id = ? 
            AND cart_detail.cart_status_id = 'A'
            AND cart.cart_status_id = 'A'
            AND MONTH(cart.cart_date) = ?
            AND YEAR(cart.cart_date) = ?
            GROUP BY department.dept_name
            ORDER BY total_value DESC
            LIMIT 10";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "iss", $stock, $selectedMonth, $selectedYear);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result_total_price_most = $stmt->get_result();

    return $result_total_price_most;
}

// ดึงข้อมูลสินค้าแยกตามแผนก และเรียงตามยอดรวมการเบิกมากที่แพงที่สุดขึ้นก่อน
function selectTotalPriceMostByDept($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql = "SELECT department.dept_name, cart_detail.prod_name, SUM(cart_detail.cart_amount) AS cart_amount, SUM(cart_detail.cart_amount * cart_detail.prod_price) AS total_value
            FROM cart_detail
            LEFT JOIN cart ON cart_detail.cart_id = cart.cart_id
            LEFT JOIN department ON cart.dept_id = department.dept_id
            WHERE cart.st_id = ? 
            AND cart_detail.cart_status_id = 'A'
            AND cart.cart_status_id = 'A'
            AND MONTH(cart.cart_date) = ?
            AND YEAR(cart.cart_date) = ?
            GROUP BY department.dept_name, cart_detail.prod_name
            ORDER BY total_value DESC 
            LIMIT 10";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "iss", $stock, $selectedMonth, $selectedYear);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result_total_price_by_dept = $stmt->get_result();

    return $result_total_price_by_dept;
}


//<----------------------------- ส่วนของ Report -------------------------------------------------->

//<----------------------------- ส่วนของ Dashboard -------------------------------------------------->

//สินค้าทั้งหมด
function selectAllProduct($conn, $stock)
{
    $sql = "SELECT SUM(prod_amount) AS total_amount 
            FROM product 
            WHERE st_id = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "i", $stock);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the total amount (if null, return 0)
    return $row['total_amount'] ?? 0;
}

//จำนวนสินค้าเหลือน้อย
function countLowStock($conn, $stock)
{
    $sql = "SELECT COUNT(*) AS low_stock_count 
            FROM product 
            WHERE st_id = ? AND prod_amount <= prod_amount_min";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "i", $stock);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the count of low stock products
    return $row['low_stock_count'] ?? 0;
}

//จำนวนสินค้าหมด stock
function countOutOfStock($conn, $stock)
{
    $sql = "SELECT COUNT(*) AS out_of_stock_count 
            FROM product 
            WHERE st_id = ? AND prod_amount = 0";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "i", $stock);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the count of out-of-stock products
    return $row['out_of_stock_count'] ?? 0;
}

//จำนวนผู้ใช้งานระบบ
function countUser($conn)
{
    $sql = "SELECT COUNT(*) AS user_count 
            FROM user 
            WHERE us_status_id = 'A' ";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    return $row['user_count'] ?? 0;
}


//จำนวนสถานะของสินค้า (Active, Inactive)
function product_status_piechart($conn, $stock)
{
    $sql = "SELECT prod_status, COUNT(*) AS product_status_count 
            FROM product 
            WHERE st_id = ?
            GROUP BY prod_status";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stock);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['prod_status']] = $row['product_status_count'];
    }

    return $data;
}


//จำนวนการอนุมัติและไม่อนุมัติ
function orderApproveAndDisapprove($conn, $stock, $selectedMonth, $selectedYear)
{
    $sql1 = "SELECT cart_status_id, COUNT(*) AS cart_status_count
            FROM cart
            WHERE st_id = ?
            AND MONTH(cart_date) = ?
            AND YEAR(cart_date) = ?
            GROUP BY cart_status_id";

    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "iss", $stock, $selectedMonth, $selectedYear);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);

    // ค่าเริ่มต้นสำหรับ Approved และ Disapproved
    $bar = ['approved' => 0, 'disapproved' => 0];

    while ($row = mysqli_fetch_assoc($result1)) {
        if ($row['cart_status_id'] === 'A') {
            $bar['approved'] = $row['cart_status_count'];
        } elseif ($row['cart_status_id'] === 'R') {
            $bar['disapproved'] = $row['cart_status_count'];
        }
    }

    return $bar;
}

//<----------------------------- ส่วนของ Dashboard -------------------------------------------------->