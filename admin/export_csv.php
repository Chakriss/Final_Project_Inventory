<?php
session_start();
include_once '../config/function.php';

if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== "loginOk") {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION["user_level"]) || $_SESSION["user_level"] !== "A") {
    header("Location: ../admin/error_admin_page.php");
    exit();
}

$stock = $_SESSION["user_stock"];
$currentMonth = date('m');
$currentYear = date('Y');
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : $currentMonth;
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

$result_total_price_by_dept = selectTotalPriceMostByDept($conn, $stock, $selectedMonth, $selectedYear);

// Set CSV headers with UTF-8 BOM
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=report_totalprice_most.csv');

// Add BOM for correct Thai encoding
echo "\xEF\xBB\xBF";

// Open file in memory for writing CSV content
$output = fopen('php://output', 'w');

// Initialize array to store department data
$departmentData = [];

// Collect data by department
if ($result_total_price_by_dept->num_rows > 0) {
    while ($row = $result_total_price_by_dept->fetch_assoc()) {
        $deptName = $row['dept_name'];

        // If department is not already in the array, initialize it
        if (!isset($departmentData[$deptName])) {
            $departmentData[$deptName] = [
                'products' => [],
                'total_price' => 0
            ];
        }

        // Add product data to the department
        $departmentData[$deptName]['products'][] = [
            'prod_name' => $row['prod_name'],
            'cart_amount' => $row['cart_amount'],
            'total_value' => $row['total_value']
        ];

        // Add to department total price
        $departmentData[$deptName]['total_price'] += $row['total_value'];
    }
}

// Write CSV data
foreach ($departmentData as $deptName => $deptData) {
    // Write department name
    fputcsv($output, [$deptName]);

    // Write headers for the product table
    fputcsv($output, ['Product', 'Quantity', 'Total Price']);

    // Write product data
    foreach ($deptData['products'] as $product) {
        fputcsv($output, [
            $product['prod_name'],
            $product['cart_amount'],
            number_format($product['total_value'], 2)
        ]);
    }

    // Write total for department
    fputcsv($output, ["Total for $deptName", '', number_format($deptData['total_price'], 2)]);
    fputcsv($output, []); // Empty row for spacing
}

// Close output
fclose($output);
exit();
