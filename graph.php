<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "DomesticTourism");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for domestic tourism expenditure
$query = "SELECT category, expenditure_by_visitors, expenditure_by_tourists FROM ExpenditureComponents";
$result = $conn->query($query);

$categories = [];
$visitors = [];
$tourists = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
    $visitors[] = $row['expenditure_by_visitors'];
    $tourists[] = $row['expenditure_by_tourists'];
}

$conn->close();

// Data for the 2010 vs 2011 tourism expenditure
$labels = ['Food & Beverages', 'Transport', 'Accommodation', 'Shopping', 'Before the Trip', 'Other Activities'];
$data2010 = [6448, 6220, 6096, 2603, 595, 1722]; // 2010 data
$data2011 = [7756, 7417, 4985, 3801, 801, 2249]; // 2011 data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graph Display: Tourism Expenditure</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        canvas {
            margin: 20px auto;
            display: block;
        }
    </style>
</head>
<body>
    <h1>Tourism Expenditure Analysis</h1>
    <p>Below are the graphical representations of different tourism expenditure data.</p>

    <!-- Section 1: Bar Chart for Domestic Tourism -->
    <h2>Domestic Tourism Expenditure by Visitors and Tourists</h2>
    <canvas id="barChart1" width="400" height="300"></canvas>

    <!-- Section 2: Pie Chart for Domestic Tourism -->
    <h2>Total Domestic Expenditure Distribution</h2>
    <canvas id="pieChart1" width="300" height="300"></canvas>

    <!-- Section 3: Bar Chart for 2010 vs 2011 Expenditure -->
    <h2>Tourism Expenditure by Component (2010 vs 2011)</h2>
    <canvas id="barChart2" width="400" height="300"></canvas>

    <!-- Section 4: Pie Chart for 2011 Expenditure Distribution -->
    <h2>Total Expenditure Distribution (2011)</h2>
    <canvas id="pieChart2" width="300" height="300"></canvas>

    <script>
        // Bar Chart for Domestic Tourism Expenditure
        const barCtx1 = document.getElementById('barChart1').getContext('2d');
        new Chart(barCtx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [
                    {
                        label: 'Visitors',
                        data: <?php echo json_encode($visitors); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Tourists',
                        data: <?php echo json_encode($tourists); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pie Chart for Domestic Tourism Expenditure
        const pieCtx1 = document.getElementById('pieChart1').getContext('2d');
        new Chart(pieCtx1, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_map(function($a, $b) { return $a + $b; }, $visitors, $tourists)); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Bar Chart for 2010 vs 2011 Expenditure
        const barCtx2 = document.getElementById('barChart2').getContext('2d');
        new Chart(barCtx2, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [
                    {
                        label: '2010 (RM million)',
                        data: <?php echo json_encode($data2010); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '2011 (RM million)',
                        data: <?php echo json_encode($data2011); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pie Chart for 2011 Expenditure Distribution
        const total2011 = <?php echo json_encode(array_sum($data2011)); ?>;
        const pieCtx2 = document.getElementById('pieChart2').getContext('2d');
        new Chart(pieCtx2, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($data2011); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#E7E9ED'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>
