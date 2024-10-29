<?php
header('Content-Type: application/json');
include_once('../config/connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $passwordNew = $_POST['password-new'];
    $passwordConfirm = $_POST['password-confirm'];

    $sql = "SELECT * FROM user WHERE us_email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($passwordNew == $passwordConfirm) {
            // แฮชรหัสผ่านใหม่ด้วย password_hash()
            $hashed_password = password_hash($passwordNew, PASSWORD_BCRYPT);

            // Check if the user exists
            if ($result->num_rows > 0) {
                $sql2 = "UPDATE user SET us_password = ? WHERE us_email = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ss", $hashed_password, $email);
                mysqli_stmt_execute($stmt2);

                $data_json = array("status" => "success");
            } else {
                $data_json = array("status" => "error", "message" => "Email Not Found.");
            }

            mysqli_stmt_close($stmt);
        } else {
            $data_json = array("status" => "error", "message" => "Passwords do not match.");
        }
    } else {
        $data_json = array("status" => "error", "message" => "Database query failed.");
    }
}

echo json_encode($data_json);

mysqli_close($conn);
