<?php

session_start();

include_once('../config/connect_db.php');
$data_json = array();

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['code'])) {
    $email = $_POST['email'];
    $password = base64_encode($_POST['password']);
    $code = $_POST['code'];


    if ($code == 'xxxxx') {

        $sql = "SELECT * FROM user WHERE us_email = ? AND us_password = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        // Execute คำสั่ง SQL
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION["login_status"] = "loginOk";
            $_SESSION["user_name"] = $row['us_name'];
            $_SESSION["user_level"] = $row['us_level'];
            $_SESSION["user_stock"] = $row['st_id'];

            if ($_SESSION["user_level"] == "Admin") {
                $data_json = array("status" => "successfully", "level" => "admin_page.php");
            } else {
                $data_json = array("status" => "successfully", "level" => "user_page.php");
            }
        } else {
            $data_json = array("status" => "Email or Password error");
            $_SESSION["login_status"] = "";
        }
    } else {
        $data_json = array("status" => "Invalid code");
    }
} else {
    $data_json = array("status" => "Missing parameters");
}

echo json_encode($data_json);

mysqli_close($conn);
