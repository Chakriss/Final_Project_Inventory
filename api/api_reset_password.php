<?php
include_once('../config/connect_db.php');

$user_id = $_POST['user_id'];
$new_password = $_POST['new_password'];

// แฮชรหัสผ่านใหม่ด้วย password_hash()
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

$sql = "UPDATE user SET us_password = ? WHERE us_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $response = array("status" => "successfully", "message" => "Password updated successfully.");
} else {
    $response = array("status" => "error", "message" => "Failed to update password: " . mysqli_error($conn));
}

echo json_encode($response);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
