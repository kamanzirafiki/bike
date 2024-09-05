<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Database connection
include '../db_connection.php';

try {
    // Fetch bookings data
    $sql = "SELECT 
                bookings.booking_id,
                bookings.booking_date,
                bookings.total_price,
                bookings.status,
                users.username AS user_name,
                bikes_scooters.name AS vehicle_name,
                pickup_station.name AS pickup_station_name,
                dropoff_station.name AS dropoff_station_name
            FROM bookings
            JOIN users ON bookings.user_id = users.user_id
            JOIN bikes_scooters ON bookings.bike_scooter_id = bikes_scooters.bike_scooter_id
            JOIN stations AS pickup_station ON bookings.pickup_station = pickup_station.station_id
            JOIN stations AS dropoff_station ON bookings.dropoff_station = dropoff_station.station_id
            ORDER BY bookings.booking_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the line chart (Bookings over the last 12 months)
    $months = [];
    $bookings_per_month = [];

    // Get current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Loop through the last 12 months
    for ($i = 11; $i >= 0; $i--) {
        $timestamp = strtotime("-$i months");
        $month = date('M Y', $timestamp);
        $months[] = $month;

        // Get year and month for the query
        $year = date('Y', $timestamp);
        $monthNum = date('m', $timestamp);

        // Prepare and execute the query to count bookings for the specific month
        $count_stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM bookings WHERE MONTH(booking_date) = :month AND YEAR(booking_date) = :year");
        $count_stmt->execute(['month' => $monthNum, 'year' => $year]);
        $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $bookings_per_month[] = $count;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            margin-left: 20%;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
            width: 150px;
            /* Adjust width as needed */
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .action-btns .btn {
            font-size: 0.8em;
            padding: 5px 10px;
        }

        .no-bookings {
            text-align: center;
            padding: 20px;
            font-size: 1em;
            color: #555;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-bar .form-group {
            margin-bottom: 10px;
        }

        .status-Pending {
            background-color: #ffc107;
            color: #fff;
            font-weight: bold;
            padding: 0.2em 0.4em;
            border-radius: 20px;
            font-size: x-small;
        }

        .status-Completed {
            background-color: #28a745;
            color: #fff;
            font-size: x-small;
            padding: 0.2em 0.4em;
            border-radius: 20px;
        }

        .status-Canceled {
            background-color: #dc3545;
            color: #fff;
            font-size: x-small;
            padding: 0.2em 0.7em;
            border-radius: 20px;
        }

        /* Chart styles */
        .chart-container {
            margin: 40px 0;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Chart Canvas */
        #bookingsChart {
            width: 100% !important;
            height: 400px !important;
        }

        @media (max-width: 768px) {
            main {
                margin-left: 0;
            }

            .search-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .chart-container {
                margin: 20px 0;
            }

            #bookingsChart {
                height: 300px !important;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff;
            }

            /* Hide sidebar and header */
            header,
            .sidebar,
            #printButton,
            .search-bar .form-group:last-child {
                display: none;
            }

            main {
                margin-left: 0;
                padding: 0;
            }

            .chart-container {
                page-break-after: avoid;
            }

            table {
                font-size: 12px;
            }

            /* Ensure the chart is visible when printed */
            canvas {
                max-width: 100%;
                height: auto !important;
            }
        }

        #printButton {
            background-color: #007bff;
            /* Blue background */
            color: #fff;
            /* White text */
            border: none;
            /* Remove border */
            border-radius: 10px;
            /* Rounded corners */
            padding: 10px 20px;
            /* Adjust padding */
            font-size: 16px;
            /* Font size */
            cursor: pointer;
            /* Pointer cursor */
            display: inline-flex;
            /* Align icon and text */
            align-items: center;
            /* Center content vertically */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            /* Optional shadow for 3D effect */
        }

        #printButton i {
            margin-right: 8px;
            /* Space between icon and text */
        }

        #printButton:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
        }
    </style>
</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Include sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main content area -->
    <main>
        <h2>View Bookings</h2>

        <div class="search-bar">
            <div class="form-group d-flex align-items-center">
                <label for="entries" class="mr-2 mb-0">Show</label>
                <select id="entries" class="form-control mr-2" style="width: 80px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="search" class="mr-2">Search:</label>
                <input type="text" id="search" class="form-control mr-2" placeholder="Search bookings...">
                <button id="printButton" class="btn">
    <i class="fas fa-print"></i> Print
</button>

            </div>
        </div>

        <?php
        if (count($bookings) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover" id="bookingsTable">';
            echo '<thead><tr><th>#</th><th>User</th><th>Vehicle</th><th>Pickup Station</th><th>Dropoff Station</th><th>Date</th><th>Total Price</th><th>Status</th></tr></thead>';
            echo '<tbody>';

            $counter = 1;
            foreach ($bookings as $booking) {
                // Determine the appropriate icon based on status
                $icon = '';
                switch (strtolower($booking['status'])) {
                    case 'pending':
                        $icon = '<i class="fas fa-hourglass-half"></i> ';
                        break;
                    case 'completed':
                        $icon = '<i class="fas fa-check-circle"></i> ';
                        break;
                    case 'canceled':
                        $icon = '<i class="fas fa-times-circle"></i> ';
                        break;
                    default:
                        $icon = '';
                }

                echo '<tr>';
                echo '<td>' . $counter . '</td>';
                echo '<td>' . htmlspecialchars($booking['user_name']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['vehicle_name']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['pickup_station_name']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['dropoff_station_name']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['booking_date']) . '</td>';
                echo '<td>' . htmlspecialchars($booking['total_price']) . '</td>';
                echo '<td><span class="status-' . htmlspecialchars($booking['status']) . '">' . $icon . htmlspecialchars($booking['status']) . '</span></td>';
                echo '</tr>';
                $counter++;
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p class="no-bookings">No bookings available.</p>';
        }
        ?>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div id="pagination-info">Showing 1 to <?php echo min(10, count($bookings)); ?> of <?php echo count($bookings); ?> entries</div>
            <nav>
                <ul class="pagination" id="pagination">
                    <!-- Pagination items will be dynamically generated -->
                </ul>
            </nav>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <h4>Bookings Over the Last 12 Months</h4>
            <canvas id="bookingsChart"></canvas>
        </div>
    </main>

    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get the data from PHP
            const labels = <?= json_encode($months) ?>;
            const bookingsData = <?= json_encode($bookings_per_month) ?>;

            const ctx = document.getElementById('bookingsChart').getContext('2d');
            const bookingsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingsData,
                        backgroundColor: 'rgba(40, 167, 69, 0.2)', // Green color
                        borderColor: 'rgba(40, 167, 69, 1)', // Green color
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4, // Smooth curves
                        pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(40, 167, 69, 1)',
                        // Limit animation duration to prevent infinite movement
                        animation: {
                            duration: 1000, // 1 second
                            easing: 'easeOutQuart'
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // Ensure whole numbers on y-axis
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });
    </script>

    <script>
        // Search functionality for filtering the table
        $(document).ready(function() {
            // Entries selection
            $('#entries').on('change', function() {
                const selected = parseInt($(this).val());
                const allRows = $('#bookingsTable tbody tr');
                const filteredRows = allRows.filter(':visible');

                allRows.hide();
                filteredRows.slice(0, selected).show();
                $('#pagination').empty();
                $('#pagination-info').text(`Showing 1 to ${Math.min(selected, filteredRows.length)} of ${filteredRows.length} entries`);
            });

            // Initialize with default entries
            $('#entries').trigger('change');

            // Search filter
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#bookingsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });

                // After filtering, update entries display
                const visibleEntries = countVisibleEntries();
                const selectedEntries = parseInt($('#entries').val());
                $('#bookingsTable tbody tr').hide();
                $('#bookingsTable tbody tr').slice(0, selectedEntries).show();
                $('#pagination').empty();
                $('#pagination-info').text(`Showing 1 to ${Math.min(selectedEntries, visibleEntries)} of ${visibleEntries} entries`);
            });

            // Function to count visible entries
            function countVisibleEntries() {
                return $('#bookingsTable tbody tr:visible').length;
            }

            // Print Button Functionality
            $('#printButton').on('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>