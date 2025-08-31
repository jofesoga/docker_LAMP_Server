<?php
require_once 'config.php';
checkRole(['admin', 'manager']);

// Default date range (last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

// Handle date filter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Get sales report
$sales_report = getSalesReport($conn, $start_date, $end_date);

// Calculate totals
$total_orders = 0;
$total_revenue = 0;

foreach ($sales_report as $day) {
    $total_orders += $day['orders'];
    $total_revenue += $day['revenue'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store - Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Sales Reports</h2>
        
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Sales Summary</h5>
                    <form method="POST" class="row g-3">
                        <div class="col-auto">
                            <label for="start_date" class="col-form-label">From:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                        </div>
                        <div class="col-auto">
                            <label for="end_date" class="col-form-label">To:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="card-text"><?php echo $total_orders; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <h2 class="card-text">$<?php echo number_format($total_revenue, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Average Daily Revenue</h5>
                                <h2 class="card-text">
                                    $<?php echo count($sales_report) > 0 ? number_format($total_revenue / count($sales_report), 2) : '0.00'; ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="col-md-4">
                        <h5>Top Selling Products</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $stmt = $conn->prepare("SELECT p.name, SUM(oi.quantity) as total_sold 
                                                             FROM order_items oi 
                                                             JOIN products p ON oi.product_id = p.product_id 
                                                             JOIN orders o ON oi.order_id = o.order_id 
                                                             WHERE DATE(o.order_date) BETWEEN ? AND ?
                                                             GROUP BY p.product_id 
                                                             ORDER BY total_sold DESC 
                                                             LIMIT 5");
                                        $stmt->execute([$start_date, $end_date]);
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<tr>
                                                <td>{$row['name']}</td>
                                                <td>{$row['total_sold']}</td>
                                            </tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode(',', array_map(function($day) { return "'" . $day['date'] . "'"; }, $sales_report)); ?>],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [<?php echo implode(',', array_map(function($day) { return $day['revenue']; }, $sales_report)); ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>