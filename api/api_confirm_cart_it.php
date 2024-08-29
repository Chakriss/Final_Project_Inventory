<?php
include_once('../config/connect_db.php');
// Set the timezone to Bangkok
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d');
$time = date('H:i:s');

if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $cart_id = $_POST['cart_id'];

    if ($code == 'xxx') {
        $cart_sql = "UPDATE cart SET cart_date = ?, cart_time = ?, cart_status_id ?";
    }
}
