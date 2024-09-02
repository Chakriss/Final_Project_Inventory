<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}
// Check the user level and include relevant files
if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "A") {
    include_once 'header.php';
    include_once 'menu_admin.php';
    include_once 'navbar.php';
    include_once 'content.php';
    include_once 'footer.php';
} else {
    // Redirect or show an error if the user level is not "User"
    header("Location: error_admin_page.php"); // Change to your actual error handling page
    exit();
}
