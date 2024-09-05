<?php
// Include the database connection file
include '../db_connection.php';

// Start the session
session_start();

// Fetch vendor ID from session
if (isset($_SESSION['vendor_id'])) {
    $vendor_id = $_SESSION['vendor_id'];
} else {
    // Redirect to login if vendor_id is not set
    header("Location: login.php");
    exit;
}

// Fetch total vehicle count
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_vehicles
    FROM bikes_scooters
    WHERE vendor_id = :vendor_id
");
$stmt->execute(['vendor_id' => $vendor_id]);
$total_vehicles = $stmt->fetchColumn();

// Fetch total booking count
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_bookings
    FROM bookings b
    INNER JOIN bikes_scooters bs ON b.bike_scooter_id = bs.bike_scooter_id
    WHERE bs.vendor_id = :vendor_id
");
$stmt->execute(['vendor_id' => $vendor_id]);
$total_bookings = $stmt->fetchColumn();

// Fetch total earnings from completed bookings
$stmt = $pdo->prepare("
    SELECT SUM(total_price) * 0.8 AS total_earnings
    FROM bookings b
    INNER JOIN bikes_scooters bs ON b.bike_scooter_id = bs.bike_scooter_id
    WHERE bs.vendor_id = :vendor_id AND b.status = 'completed'
");
$stmt->execute(['vendor_id' => $vendor_id]);
$total_earnings = $stmt->fetchColumn();

// Initialize arrays for Chart.js
$vehicle_data_map = [];
$booking_data_map = [];

// Initialize all months with zero counts
$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
foreach ($months as $month) {
    $vehicle_data_map[$month] = 0;
    $booking_data_map[$month] = 0;
}

// Fetch monthly vehicle data for the chart
$stmt = $pdo->prepare("
    SELECT MONTHNAME(created_at) AS month, COUNT(*) AS vehicle_count
    FROM bikes_scooters
    WHERE vendor_id = :vendor_id
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)
");
$stmt->execute(['vendor_id' => $vendor_id]);
$monthly_vehicles_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch monthly booking data for the chart
$stmt = $pdo->prepare("
    SELECT MONTHNAME(b.created_at) AS month, COUNT(*) AS booking_count
    FROM bookings b
    INNER JOIN bikes_scooters bs ON b.bike_scooter_id = bs.bike_scooter_id
    WHERE bs.vendor_id = :vendor_id
    GROUP BY MONTH(b.created_at)
    ORDER BY MONTH(b.created_at)
");
$stmt->execute(['vendor_id' => $vendor_id]);
$monthly_bookings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fill arrays with actual data for the chart
foreach ($monthly_vehicles_data as $row) {
    $vehicle_data_map[$row['month']] = (int)$row['vehicle_count'];
}
foreach ($monthly_bookings_data as $row) {
    $booking_data_map[$row['month']] = (int)$row['booking_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            height: 100px; 
            color: grey; /* Text color */
            flex: 1 1 30%; /* Ensure cards take up equal space */
        }
        .card .indicator {
            width: 10px;
            height: 100%;
            border-radius: 15px 0 0 15px;
            position: absolute;
            left: 0;
        }
        .card-bookings .indicator {
            background-color: #ff3b5f; /* Slightly darker shade */
        }
        .card-vehicles .indicator {
            background-color: #218de8; /* Slightly darker shade */
        }
        .card-earnings .indicator {
            background-color: #28a745; /* Green for earnings */
        }
        .icon {
            font-size: 30px;
            color: grey;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .card-title {
            font-weight: 600;
            font-size: 16px;
        }
        .card h3 {
            font-weight: bold;
            margin: 10px 0;
        }
        .text-muted {
            font-size: 12px;
            color: #e0e0e0; /* Lightened for better contrast */
        }
        .row {
            display: flex;
            flex-wrap: wrap; /* Ensure cards wrap to the next line */
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container main-content">
    <div class="row">
        <!-- Total Bookings Card -->
        <div class="col-md-4">
            <div class="card card-bookings">
                <div class="indicator"></div>
                <div class="card-body">
                    <h5 class="card-title">TOTAL BOOKINGS</h5>
                    <h3><?php echo $total_bookings; ?></h3>
                    <i class="bi bi-calendar-check icon"></i>
                    <p class="text-muted">Total bookings made</p>
                </div>
            </div>
        </div>
        <!-- Total Vehicles Card -->
        <div class="col-md-4">
            <div class="card card-vehicles">
                <div class="indicator"></div>
                <div class="card-body">
                    <h5 class="card-title">TOTAL VEHICLES</h5>
                    <h3><?php echo $total_vehicles; ?></h3>
                    <i class="bi bi-bicycle icon"></i>
                    <p class="text-muted">Total vehicles available</p>
                </div>
            </div>
        </div>
        <!-- Total Earnings Card -->
<div class="col-md-4">
    <div class="card card-earnings">
        <div class="indicator"></div>
        <div class="card-body">
            <h5 class="card-title">TOTAL EARNINGS (RWF)</h5>
            <!-- Ensure decimal precision is displayed using 2 decimal places -->
            <h3><?php echo number_format($total_earnings, 2); ?></h3>
            <i class="bi bi-cash-coin icon"></i>
            <p class="text-muted">Total earnings from bookings</p>
        </div>
    </div>
</div>

    </div>

    <!-- Line Chart Section -->
    <div class="row mt-4">
        <div class="col-12">
            <canvas id="myLineChart"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('myLineChart').getContext('2d');
    const myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [
                {
                    label: 'Vehicles',
                    data: <?php echo json_encode(array_values($vehicle_data_map)); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: false,  // Ensure no fill for this line
                    tension: 0.1,
                    borderWidth: 2,
                    pointStyle: 'circle',
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Bookings',
                    data: <?php echo json_encode(array_values($booking_data_map)); ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: false,  // Ensure no fill for this line
                    tension: 0.1,
                    borderWidth: 2,
                    pointStyle: 'rect',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    mode: 'index', // Display all data points along x-axis
                    intersect: false, // Show tooltip for each point even if they don't overlap
                    callbacks: {
                        label: function(tooltipItem) {
                            // Customize the tooltip label to show counts
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                },
                legend: {
                    position: 'top',
                },
            },
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
