<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "alcohol_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total sales for each month of the current year
$sql = "SELECT DATE_FORMAT(datetime, '%Y-%m') AS month, SUM(total) as total_sales
        FROM checkout
        WHERE YEAR(datetime) = YEAR(CURDATE())
        GROUP BY month
        ORDER BY month";

$result = $conn->query($sql);

$data = [];
$labels = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['month'];  // Store month (formatted as YYYY-MM)
        $data[] = $row['total_sales'];  // Store total sales for the month
    }
} else {
    $no_data = true;  // Set flag to true if no data is found
}

$conn->close();

include('admin_header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Sales Transaction</title>
    <link rel="stylesheet" href="css/monthly_transaction.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/menu.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>
<body>
<div class="container">
<h2>Monthly Sales Transaction</h2>

<!-- Check if there is no data and display the message in a div -->
<?php if (isset($no_data) && $no_data): ?>
    <div class="no-data-message">
        <p>No sales data found for this year.</p>
    </div>
<?php else: ?>
    <!-- Table displaying total sales per month -->
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Sales (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Output the sales data in table format
            foreach ($labels as $index => $month) {
                echo "<tr><td>" . $month . "</td><td>RM" . number_format($data[$index], 2) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br><br>

    <!-- Pie chart displaying total sales per month -->
    <canvas id="salesChart" width="400" height="400"></canvas>

    <script>
        // PHP array data passed to JavaScript
        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($data); ?>;

        var ctx = document.getElementById('salesChart').getContext('2d');
        var totalSales = data.reduce((a, b) => a + b, 0); // Calculate the total of all sales

        var salesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Sales',
                    data: data,
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FFFF33', '#FF33FF', '#FF8C00', '#00CED1', '#8A2BE2', '#7FFF00', '#FF1493'],
                    borderColor: ['#ffffff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                // Show total sales amount and percentage in the tooltip
                                var percentage = (tooltipItem.raw / totalSales * 100).toFixed(2);
                                return tooltipItem.label + ': RM' + tooltipItem.raw.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    },
                    // Display the total sales amount and percentage on the chart itself
                    datalabels: {
                        formatter: function(value, context) {
                            var percentage = (value / totalSales * 100).toFixed(2); // Calculate percentage
                            return 'RM' + value.toFixed(2) + '\n(' + percentage + '%)'; // Combine sales and percentage
                        },
                        color: '#ffffff',  // Text color for the labels
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    }
                }
            }
        });
    </script>
<?php endif; ?>

</div>
</body>
</html>