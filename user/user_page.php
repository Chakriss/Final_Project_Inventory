<?php
session_start();
include_once '../config/function.php'; // Include function to connect to the database

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}

// Check the user level and include relevant files
if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "U") {
    include_once '../header.php';
    include_once 'menu_user.php';
    include_once '../navbar.php';
    include_once 'content_user.php';
    include_once '../footer.php';
} else {
    // Redirect or show an error if the user level is not "User"
    header("Location: ../user/error_user_page.php"); // Change to your actual error handling page
    exit();
}

// Fetch stock information
$stock = $_SESSION["user_stock"]; // Get the user stock

// Initialize counts for low stock
$lowStockCount1 = 0;
$lowStockCount2 = 0;

// Fetch product data based on stock level
if ($stock == 3) {
    // Fetch product data for stock level 1
    $sql1 = "SELECT * FROM product WHERE st_id = 1 AND prod_amount <= prod_amount_min";
    $result1 = mysqli_query($conn, $sql1);
    $lowStockCount1 = mysqli_num_rows($result1);

    // Fetch product data for stock level 2
    $sql2 = "SELECT * FROM product WHERE st_id = 2 AND prod_amount <= prod_amount_min";
    $result2 = mysqli_query($conn, $sql2);
    $lowStockCount2 = mysqli_num_rows($result2);
} else {
    // For other stock levels (1 or 2), fetch accordingly
    $sql = "SELECT * FROM product WHERE st_id = '$stock' AND prod_amount <= prod_amount_min";
    $result = mysqli_query($conn, $sql);
    $lowStockCount1 = mysqli_num_rows($result);
}

// Check if the email has been sent today
$currentDate = date("Y-m-d");
$emailSentToday = isset($_SESSION['email_sent_date']) && $_SESSION['email_sent_date'] === $currentDate;
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Check if we need to send the email
        <?php if (($lowStockCount1 > 0 || $lowStockCount2 > 0) && !$emailSentToday): ?>
            sendEmailWithCSV();
        <?php endif; ?>
    });

    function sendEmailWithCSV() {
        // Use two separate AJAX calls to get data for both stock levels
        var lowStockData = [];

        // Fetch stock level 1 data
        $.ajax({
            url: "../api/api_export_stock_low2.php?level=1", // Modify your endpoint to handle stock level
            type: 'GET',
            dataType: 'json',
            success: function(response1) {
                if (response1.status === 'success') {
                    lowStockData = lowStockData.concat(response1.data);
                    
                    // Check if the user stock is 3 to fetch stock level 2 data
                    <?php if ($stock == 3): ?>
                        // Fetch stock level 2 data
                        $.ajax({
                            url: "../api/api_export_stock_low2.php?level=2", // Modify your endpoint to handle stock level
                            type: 'GET',
                            dataType: 'json',
                            success: function(response2) {
                                if (response2.status === 'success') {
                                    lowStockData = lowStockData.concat(response2.data);
                                    prepareAndSendCSV(lowStockData);
                                } else {
                                    alert('Error fetching stock level 2: ' + response2.message);
                                }
                            },
                            error: function() {
                                alert('An error occurred while fetching stock level 2 data.');
                            }
                        });
                    <?php else: ?>
                        // If stock is not 3, send the CSV with just stock level 1 data
                        prepareAndSendCSV(lowStockData);
                    <?php endif; ?>
                } else {
                    alert('Error fetching stock level 1: ' + response1.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching stock level 1 data.');
            }
        });
    }

    function prepareAndSendCSV(data) {
        // Prepare the CSV content
        var csvContent = "Id,Name,Quantity,Unit\n"; // CSV Header
        data.forEach(function(item) {
            csvContent += item.prod_id + "," + item.prod_name + "," + item.prod_amount + "," + item.prod_unit + "\n";
        });

        // Send the CSV content to the server for email
        $.ajax({
            url: "../api/api_send_email_with_csv2.php",
            type: 'POST',
            data: {
                csvData: csvContent,
                fileName: 'product_low.csv'
            },
            success: function(emailResponse) {
                console.log(emailResponse); // Log the full response for debugging

                try {
                    var responseJson = JSON.parse(emailResponse);
                    if (responseJson.status === 'success') {
                        // Mark that the email has been sent today
                        <?php $_SESSION['email_sent_date'] = $currentDate; ?>
                    } else {
                        var errorMessage = responseJson.message || 'Unknown error';
                    }
                } catch (e) {
                    // alert('Error parsing response: ' + emailResponse);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error details:', textStatus, errorThrown);
                alert('An error occurred while sending the email.');
            }
        });
    }
</script>
