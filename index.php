<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
} else {
    header("Location: logout.php");
    exit();
}
