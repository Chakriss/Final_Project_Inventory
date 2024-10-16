<?php
session_start();
include_once('../config/connect_db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Load Composer's autoloader

// Fetch stock information
$stock = $_SESSION["user_stock"];
$data1 = [];
$data2 = [];

// Prepare the SQL queries
if ($stock == 3) {
    // Check stock levels 1 and 2
    $query1 = "SELECT * FROM product WHERE st_id = 1 AND prod_amount <= prod_amount_min";
    $query2 = "SELECT * FROM product WHERE st_id = 2 AND prod_amount <= prod_amount_min";

    // Fetch stock level 1 data
    $stmt1 = mysqli_prepare($conn, $query1);
    if (mysqli_stmt_execute($stmt1)) {
        $result1 = mysqli_stmt_get_result($stmt1);
        while ($row = mysqli_fetch_assoc($result1)) {
            $data1[] = $row; // Collect stock level 1 data
        }
    }
    mysqli_stmt_close($stmt1);

    // Fetch stock level 2 data
    $stmt2 = mysqli_prepare($conn, $query2);
    if (mysqli_stmt_execute($stmt2)) {
        $result2 = mysqli_stmt_get_result($stmt2);
        while ($row = mysqli_fetch_assoc($result2)) {
            $data2[] = $row; // Collect stock level 2 data
        }
    }
    mysqli_stmt_close($stmt2);
} else {
    // Check only the current stock level
    $query = "SELECT * FROM product WHERE st_id = ? AND prod_amount <= prod_amount_min";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $stock);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $data1[] = $row; // Collect current stock level data
        }
    }
    mysqli_stmt_close($stmt);
}

// Create CSV files and send emails only if there is data
if ($stock == 3) {
    // Create CSV for stock level 1 if data exists
    if (!empty($data1)) {
        $file1 = 'stock_level_1.csv';
        $fp1 = fopen($file1, 'w');

        // Set header
        fputcsv($fp1, ['Id', 'Name', 'Quantity', 'Unit']);

        // Fill data
        foreach ($data1 as $item) {
            fputcsv($fp1, [$item['prod_id'], $item['prod_name'], $item['prod_amount'], $item['prod_unit']]);
        }

        fclose($fp1);

        // Send email with attachment for stock level 1
        $Recipient_email1 = 'chanori40@gmail.com'; // For stock level 1
        $mail1 = new PHPMailer(true);
        try {
            // Server settings
            $mail1->isSMTP();
            $mail1->Host = 'smtp.gmail.com';
            $mail1->SMTPAuth = true;
            $mail1->Username = 'phantuwech.ch@gmail.com';
            $mail1->Password = 'rcie cgek rkbe tnkl'; // Use an app-specific password for Gmail
            $mail1->SMTPSecure = 'tls';
            $mail1->Port = 587;

            // Recipients
            $mail1->setFrom('phantuwech.ch@gmail.com', 'Inventory');
            $mail1->addAddress($Recipient_email1); // Only for stock level 1

            // Attachments
            if (file_exists($file1)) {
                $mail1->addAttachment($file1);
            }

            // Content
            $mail1->isHTML(true);
            $mail1->CharSet = 'UTF-8';
            $mail1->Subject = 'Product Low Stock Level 1';
            $mail1->Body    = 'Please find attached the product low stock data for level 1 in CSV format.';

            $mail1->send();
            echo json_encode(['status' => 'success', 'message' => 'Email for stock level 1 sent successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Email for stock level 1 could not be sent. Mailer Error: ' . $mail1->ErrorInfo]);
        }
    }

    // Create CSV for stock level 2 if data exists
    if (!empty($data2)) {
        $file2 = 'stock_level_2.csv';
        $fp2 = fopen($file2, 'w');

        // Set header
        fputcsv($fp2, ['Id', 'Name', 'Quantity', 'Unit']);

        // Fill data
        foreach ($data2 as $item) {
            fputcsv($fp2, [$item['prod_id'], $item['prod_name'], $item['prod_amount'], $item['prod_unit']]);
        }

        fclose($fp2);

        // Send email with attachment for stock level 2
        $Recipient_email2 = '64310148@go.buu.ac.th'; // For stock level 2
        $mail2 = new PHPMailer(true);
        try {
            // Server settings
            $mail2->isSMTP();
            $mail2->Host = 'smtp.gmail.com';
            $mail2->SMTPAuth = true;
            $mail2->Username = 'phantuwech.ch@gmail.com';
            $mail2->Password = 'rcie cgek rkbe tnkl'; // Use an app-specific password for Gmail
            $mail2->SMTPSecure = 'tls';
            $mail2->Port = 587;

            // Recipients
            $mail2->setFrom('phantuwech.ch@gmail.com', 'Inventory');
            $mail2->addAddress($Recipient_email2); // Only for stock level 2

            // Attachments
            if (file_exists($file2)) {
                $mail2->addAttachment($file2);
            }

            // Content
            $mail2->isHTML(true);
            $mail2->CharSet = 'UTF-8';
            $mail2->Subject = 'Product Low Stock Level 2';
            $mail2->Body    = 'Please find attached the product low stock data for level 2 in CSV format.';

            $mail2->send();
            echo json_encode(['status' => 'success', 'message' => 'Email for stock level 2 sent successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Email for stock level 2 could not be sent. Mailer Error: ' . $mail2->ErrorInfo]);
        }
    }

    // Delete the CSV files after sending if they exist
    if (file_exists($file1)) {
        unlink($file1);
    }
    if (file_exists($file2)) {
        unlink($file2);
    }
} else {
    // Logic for sending the current stock level data can go here...
}

// Close the connection
mysqli_close($conn);
