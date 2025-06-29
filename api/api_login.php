<?php
session_start();
include_once('../config/connect_db.php');
$data_json = array();

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['code'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $code = trim($_POST['code']);
    $status = 'A';

    if ($code === 'xxxxx') {
        $sql = "SELECT * FROM user WHERE us_email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Check if the user exists
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Verify the password using password_verify()
                if (password_verify($password, $row['us_password'])) {
                    if ($row['us_status_id'] === $status) {
                        // Set session variables
                        $_SESSION["login_status"] = "loginOk";
                        $_SESSION["user_name"] = $row['us_name'];
                        $_SESSION["user_level"] = $row['us_level_id'];
                        $_SESSION["user_stock"] = $row['st_id'];
                        $_SESSION["user_id"] = $row['us_id'];
                        $_SESSION["user_dept"] = $row['dept_id'];

                        // Redirect based on user level
                        if ($_SESSION["user_level"] === "A") {
                            $data_json = array("status" => "successfully", "level" => "admin/admin_page.php");
                        } else {
                            $data_json = array("status" => "successfully", "level" => "user/user_page.php");
                        }
                    } else {
                        $data_json = array("status" => "This account has been deactivated.");
                    }
                } else {
                    $data_json = array("status" => "Email or Password error");
                    $_SESSION["login_status"] = "";
                }
            } else {
                $data_json = array("status" => "Email or Password error");
                $_SESSION["login_status"] = "";
            }
        } else {
            $data_json = array("status" => "Database query failed.");
        }

        $stmt->close();
    } else {
        $data_json = array("status" => "Invalid code");
    }
} else {
    $data_json = array("status" => "Missing parameters");
}

echo json_encode($data_json);
mysqli_close($conn);
