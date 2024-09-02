<?php

session_start();

include_once('../config/connect_db.php');

$id = $_POST['id'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = base64_encode($_POST['password']);
$permission = $_POST['permission'];
$dept = $_POST['dept'];
$stock = $_POST['stock'];
$status = $_POST['status'];

$data_json = array();

if (isset($_POST['id'])) {

    // Check for duplicate user email
    $duplicate_check_sql = "SELECT COUNT(*) FROM user WHERE us_email = ? AND us_id != ?";
    $duplicate_stmt = mysqli_prepare($conn, $duplicate_check_sql);
    mysqli_stmt_bind_param($duplicate_stmt, "si", $email, $id);
    mysqli_stmt_execute($duplicate_stmt);
    mysqli_stmt_bind_result($duplicate_stmt, $count);
    mysqli_stmt_fetch($duplicate_stmt);
    mysqli_stmt_close($duplicate_stmt);

    if ($count > 0) {
        $data_json = array("status" => "error", "message" => "A user with this email already exists.");
        echo json_encode($data_json);
        exit();
    }

    // Update user information
    $sql = "UPDATE user SET us_name = ?, us_email = ?, us_password = ?, us_level_id = ?, dept_id = ?, st_id = ?, us_status_id = ? WHERE us_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssiisi", $name, $email, $password, $permission, $dept, $stock, $status, $id);

    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        // Check if the updated user is the current logged-in user
        if ($id == $_SESSION["user_id"]) {
            $data_json = array("status" => "login_required", "message" => "Your profile has been updated. Please log in again.");
        } else {
            $data_json = array("status" => "successfully", "message" => "Admin updated successfully.");
        }
    } else {
        $data_json = array("status" => "error", "message" => "Failed to update admin: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);

} else {
    $data_json = array("status" => "error", "message" => "Admin ID not found.");
}

echo json_encode($data_json);

mysqli_close($conn);
?>
