<?php
session_start();
include_once 'config/function.php';

// Check if the user is logged in
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: login.php");
    exit();
}

// Check the user level
if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "A") {
    // User level is valid
} else {
    header("Location: admin/error_admin_page.php");
    exit();
}

if ($_SESSION["user_level"] === "A") {
    $page = 'admin/admin_page.php';
} else {
    $page = 'user/user_page.php';
}

$stock = $_SESSION["user_stock"];

// Check if the email has been sent today
$currentDate = date("Y-m-d");
if (isset($_SESSION['email_sent_date']) && $_SESSION['email_sent_date'] === $currentDate) {
    // Redirect to index.php if email was already sent today
    header("Location: $page");
    exit();
}

// Fetch product data
$sql = "SELECT * FROM product WHERE st_id = '$stock' AND prod_amount <= prod_amount_min";
$result = mysqli_query($conn, $sql);

// Check if there are any products with low stock
$lowStockCount = mysqli_num_rows($result);
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Check if there are low stock products and call the function
    <?php if ($lowStockCount > 0): ?>
        sendEmailWithCSV();
    <?php endif; ?>

    function sendEmailWithCSV() {
        $.ajax({
            url: "api/api_export_stock_low.php", // Endpoint to fetch all data
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;

                    // Prepare the CSV content
                    var csvContent = "Id,Name,Quantity,Unit\n"; // CSV Header
                    data.forEach(function(item) {
                        csvContent += item.prod_id + "," + item.prod_name + "," + item.prod_amount + "," + item.prod_unit + "\n";
                    });

                    // Send the CSV content to the server for email
                    $.ajax({
                        url: "api/api_send_email_with_csv.php",
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
                                    // Redirect to index.php after successful email send
                                    window.location.href = '<?php echo $page; ?>';

                                } else {
                                    var errorMessage = responseJson.message || 'Unknown error';
                                    window.location.href = '<?php echo $page; ?>';

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
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching the data.');
            }
        });
    }
</script>