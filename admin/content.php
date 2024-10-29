<?php
// Set default month and year to current month and year
$currentMonth = date('m');
$currentYear = date('Y');
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : $currentMonth;
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

$stock = $_SESSION["user_stock"];
$result_total_amount = selectAllProduct($conn, $stock);
$low_stock_count = countLowStock($conn, $stock);
$out_of_stock_count = countOutOfStock($conn, $stock);
$user_count = countUser($conn);
$product_status_pinechart = product_status_piechart($conn, $stock);
$orderApprovalData = orderApproveAndDisapprove($conn, $stock, $selectedMonth, $selectedYear);
$approvedCount = $orderApprovalData['approved'];
$disapprovedCount = $orderApprovalData['disapproved'];

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
        /* Smooth transition for hover effect */
    }

    .card:hover {
        transform: translateY(-5px);
        /* Lift effect on hover */
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        /* Deeper shadow on hover */
    }

    .stats-icon {
        font-size: 30px;
        /* Increased icon size */
        color: #ffffff;
        padding: 15px;
        /* Increased padding for better spacing */
        border-radius: 50%;
        margin-bottom: 10px;
    }

    body {
        background-color: #f0f2f5;
        /* พื้นหลังสีเทาอ่อน */
        color: #333333;
    }

    .chart-card {
        display: flex;
        /* Use Flexbox */
        flex-direction: column;
        /* Arrange children vertically */
        justify-content: center;
        /* Center content vertically */
        align-items: center;
        /* Center content horizontally */
        background-color: #ffffff;
        /* White background for chart card */
        border: 1px solid #e0e0e0;
        /* Subtle border */
        padding: 20px;
        /* Add padding for spacing */
        height: 300px;
        /* Set a fixed height for the card */
    }

    .chart-card canvas {
        max-width: 100%;
        /* Make sure the canvas fits within the card */
        height: auto;
        /* Maintain aspect ratio */
    }


    .card-body {
        padding: 30px;
        /* Uniform padding for all card bodies */
    }

    h5 {
        font-weight: bold;
        /* Bold heading */
        color: #333333;
        /* Dark color for headings */
    }

    h6 {
        margin: 0;
        /* Remove default margin */
    }

    .text-muted {
        font-size: 0.9em;
        /* Slightly smaller font for muted text */
    }

    .button {
        transition: background-color 0.3s;
        /* Smooth transition for button hover */
    }

    .button:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }

    .card-header {
        background: white;
        /* Gradient background */
        border-bottom: 2px solid #ddd;
        padding: 20px;
        border-radius: 8px 8px 0 0;
        box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        color: black;
        /* White text color to contrast with the gradient background */
    }

    .chart-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        padding: 20px;
        height: 400px;
        /* เพิ่มความสูง */
        width: 100%;
        /* เพิ่มความกว้าง */
    }

    #chart {
        max-width: 100%;
        /* ทำให้แน่ใจว่ากราฟแท่งอยู่ในขอบเขต */
        height: 300px;
        /* ตั้งความสูงของกราฟแท่ง */
    }
</style>



<div id="main">
    <div class="page-heading">
        <section class="section">
            <div class="card-header" style="margin-bottom: 20px;">
                <form method="POST" action="">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="month" class="mb-1">Select Month:</label>
                            <select id="month" name="month" class="form-control">
                                <?php
                                for ($m = 1; $m <= 12; $m++) {
                                    $monthValue = sprintf('%02d', $m);
                                    $selected = ($monthValue == $selectedMonth) ? 'selected' : '';
                                    echo "<option value='$monthValue' $selected>" . date('F', mktime(0, 0, 0, $m, 10)) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="mb-1">Select Year:</label>
                            <select id="year" name="year" class="form-control">
                                <?php
                                for ($y = 2020; $y <= date('Y'); $y++) {
                                    $selected = ($y == $selectedYear) ? 'selected' : '';
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>


    <div class="page-content">
        <div class="row justify-content-center mb-4">
            <div class="col-6 col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4-5">
                        <div class="stats-icon purple mb-2 mx-auto">
                            <i class="iconly-boldBag-2"></i>
                        </div>
                        <h6 class="text-muted font-semibold">สินค้าคงเหลือ</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $result_total_amount; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4-5">
                        <div class="stats-icon blue mb-2 mx-auto">
                            <i class="iconly-boldNotification"></i>
                        </div>
                        <h6 class="text-muted font-semibold">สินค้าเหลือน้อย</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $low_stock_count; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4-5">
                        <div class="stats-icon red mb-2 mx-auto">
                            <i class="iconly-boldDanger"></i>
                        </div>
                        <h6 class="text-muted font-semibold">สินค้าหมด</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $out_of_stock_count; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4-5">
                        <div class="stats-icon green mb-2 mx-auto">
                            <i class="iconly-boldUser"></i>
                        </div>
                        <h6 class="text-muted font-semibold">จำนวนผู้ใช้งาน</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $user_count; ?></h6>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <!-- Bar Chart Card - Left Column -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card chart-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-center">การอนุมัติและไม่อนุมัติ</h5>
                            <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                                <div id="chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doughnut Chart Card - Right Column -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card chart-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-center">สถานะของสินค้า</h5>
                            <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                                <canvas id="productStatusPieChart" width="220" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <script>
                // PHP data passed to JavaScript
                const productStatusData = <?php echo json_encode($product_status_pinechart); ?>;

                // Prepare data for Chart.js
                const data = {
                    labels: ['Active', 'Inactive'],
                    datasets: [{
                        label: 'Status',
                        data: [productStatusData['A'] || 0, productStatusData['I'] || 0],
                        backgroundColor: ['#1E88E5', '#4FC3F7'],
                        hoverOffset: 4
                    }]
                };

                // Chart configuration
                const config = {
                    type: 'doughnut',
                    data: data,
                    options: {
                        responsive: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                enabled: true
                            },
                            datalabels: {
                                formatter: (value, context) => {
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = (value / total * 100).toFixed(1);
                                    return percentage + '%';
                                },
                                color: '#ffffff',
                                font: {
                                    weight: 'bold',
                                    size: 14
                                }
                            }
                        },
                        cutout: '50%'
                    },
                    plugins: [ChartDataLabels]
                };

                // Render the chart
                new Chart(
                    document.getElementById('productStatusPieChart'),
                    config
                );


                // <-----barchart ------>

                const approvedCount = <?php echo json_encode($approvedCount); ?>;
                const disapprovedCount = <?php echo json_encode($disapprovedCount); ?>;

                const options = {
                    chart: {
                        type: 'bar',
                        height: 300,
                        width: '150%',
                    },
                    series: [{
                        name: 'Order',
                        data: [approvedCount, disapprovedCount]
                    }],
                    colors: ['#008FFB', '#FF4560'], // สีของแต่ละแท่ง
                    xaxis: {
                        categories: ['Approved', 'Disapproved']
                    },
                    title: {
                        text: 'Order'
                    },
                    plotOptions: {
                        bar: {
                            distributed: true, // ทำให้แต่ละแท่งมีสีที่แตกต่างกันตามลำดับใน colors
                            dataLabels: {
                                position: 'center' // วาง label ไว้ตรงกลางของแท่ง 
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: 0, // วาง label ไว้ตรงกลางของแท่ง
                        style: {
                            fontSize: '12px',
                            colors: ["#FFFFFF"] // ตั้งค่าสีของตัวเลขให้ชัดเจน
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            </script>