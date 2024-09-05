<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Database connection
require '../db_connection.php';

$subscribers = [];

try {
    // Fetch subscribers data for the table
    $stmt = $pdo->prepare("SELECT id, email, subscribed_at FROM subscribers ORDER BY subscribed_at DESC");
    $stmt->execute();
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the line chart (Subscriber Registrations over the last 12 months)
    $months = [];
    $subscribers_per_month = [];

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

        // Prepare and execute the query to count subscribers for the specific month
        $count_stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM subscribers WHERE MONTH(subscribed_at) = :month AND YEAR(subscribed_at) = :year");
        $count_stmt->execute(['month' => $monthNum, 'year' => $year]);
        $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;  // Ensure count is at least 0
        $subscribers_per_month[] = (int)$count;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers List</title>
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
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-subscribers {
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
            margin: 0;
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
        #subscribersChart {
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

            #subscribersChart {
                height: 300px !important;
            }
        }

        /* Pagination Styles */
        .pagination {
            margin: 0;
        }
        #printButton {
            background-color: #007bff; /* Blue background */
            color: #fff; /* White text */
            border: none; /* Remove border */
            border-radius: 10px; /* Rounded corners */
            padding: 10px 20px; /* Adjust padding */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor */
            display: inline-flex; /* Align icon and text */
            align-items: center; /* Center content vertically */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Optional shadow for 3D effect */
        }

        #printButton i {
            margin-right: 8px; /* Space between icon and text */
        }

        #printButton:hover {
            background-color: #0056b3; /* Darker blue on hover */
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
        <!-- Success/Error Message Display -->
        <?php
        if (isset($_GET['message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>

        <h2>Subscribers List</h2>

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
                <label for="search" class="mr-2 mb-0">Search:</label>
                <input type="text" id="search" class="form-control mr-4" placeholder="Search vehicles...">
                <button class="custom-print-btn" id="printButton"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <?php
        if (count($subscribers) > 0) {
            echo '<div class="table-container">';
            echo '<table class="table table-bordered table-hover" id="subscribersTable">';
            echo '<thead><tr><th>#</th><th>Email</th><th>Subscription Date</th><th>Action</th></tr></thead>';
            echo '<tbody>';

            foreach ($subscribers as $subscriber) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($subscriber['id']) . '</td>';
                echo '<td>' . htmlspecialchars($subscriber['email']) . '</td>';
                echo '<td>' . htmlspecialchars($subscriber['subscribed_at']) . '</td>';
                echo '<td>';
                // Delete Button to trigger modal
                echo '<button class="btn btn-danger btn-sm delete-btn" data-id="' . htmlspecialchars($subscriber['id']) . '" data-email="' . htmlspecialchars($subscriber['email']) . '" title="Delete">';
                echo '<i class="bi bi-trash"></i> Delete';
                echo '</button>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p class="no-subscribers">No subscribers found.</p>';
        }
        ?>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div id="pagination-info">Showing 1 to <?php echo min(10, count($subscribers)); ?> of <?php echo count($subscribers); ?> entries</div>
            <nav>
                <ul class="pagination" id="pagination">
                    <!-- Pagination items will be dynamically generated -->
                </ul>
            </nav>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <h4>Subscriber Registrations Over the Last 12 Months</h4>
            <canvas id="subscribersChart"></canvas>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the subscriber <strong id="subscriberEmail"></strong>?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="deletesub.php" method="POST">
                        <input type="hidden" name="id" id="subscriberId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Handle delete button click
        $(document).on('click', '.delete-btn', function () {
            const subscriberId = $(this).data('id');
            const subscriberEmail = $(this).data('email');
            
            $('#subscriberId').val(subscriberId); // Set the hidden input with the ID
            $('#subscriberEmail').text(subscriberEmail); // Set the email in the modal

            $('#deleteModal').modal('show'); // Show the delete confirmation modal
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Get the data from PHP
            const labels = <?= json_encode($months) ?>;
            const subscribersData = <?= json_encode($subscribers_per_month) ?>;

            const ctx = document.getElementById('subscribersChart').getContext('2d');
            const subscribersChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Subscribers',
                        data: subscribersData,
                        backgroundColor: 'rgba(23, 162, 184, 0.2)', 
                        borderColor: 'rgba(23, 162, 184, 1)', 
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4, 
                        pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(23, 162, 184, 1)',
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
                        x: {
                            title: {
                                display: true,
                                text: 'Month', // X-axis label
                                font: {
                                    size: 16 // Optional: font size for the label
                                }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Subscribers', // Y-axis label
                                font: {
                                    size: 16 // Optional: font size for the label
                                }
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0, // Ensure whole numbers on y-axis
                                stepSize: 1,
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y + ' subscribers';
                                }
                            }
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
</body>

</html>
