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
            product_status.prod_status_desc, 
            product_type.prod_type_desc 
        FROM product
        LEFT JOIN product_status ON product.prod_status = product_status.prod_status 
        LEFT JOIN product_type ON product.prod_type_id = product_type.prod_type_id
        WHERE st_id = ?";

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

//ฟังชันดึงข้อมูลประเภทของสินค้า
function selectType($conn)
{
    $sql_type = "SELECT * FROM product_type";
    $stmt = mysqli_prepare($conn, $sql_type);
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
