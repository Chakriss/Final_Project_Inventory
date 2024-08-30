<?php
include_once 'connect_db.php';

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


//<----------------------------- ส่วนของ cart -------------------------------------------------->
function cartDetail($conn)
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
