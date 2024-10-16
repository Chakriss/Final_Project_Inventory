<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Load Composer's autoloader

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csvData = $_POST['csvData'];
    $fileName = $_POST['fileName'];

    // Create a temporary file
    $tempFilePath = tempnam(sys_get_temp_dir(), 'csv_');
    file_put_contents($tempFilePath, $csvData); // Save the CSV content to the temp file

    $stock = $_SESSION["user_stock"];
    $Recipient_email = ($stock == 1) ? 'Chanori40@gmail.com' : '64310148@go.buu.ac.th';

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phantuwech.ch@gmail.com';
        $mail->Password = 'rcie cgek rkbe tnkl'; // Make sure to use an app-specific password for Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('phantuwech.ch@gmail.com', 'Inventory');
        $mail->addAddress($Recipient_email);

        // Attachments
        $mail->addAttachment($tempFilePath, $fileName);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Product Low Stock';
        $mail->Body    = 'Please find attached the product low stock data in CSV format.';

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    }

    // Delete the temporary file
    unlink($tempFilePath);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
