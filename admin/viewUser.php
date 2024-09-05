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
    // Fetch users data
    $sql = "SELECT user_id, username, email, created_at, is_active FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the line chart (User Registrations over the last 12 months)
    $months = [];
    $users_per_month = [];

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

        // Prepare and execute the query to count users for the specific month
        $count_stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year");
        $count_stmt->execute(['month' => $monthNum, 'year' => $year]);
        $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $users_per_month[] = $count;
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
    <title>View Users</title>
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
            width: 150px; /* Adjust width as needed */
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .action-btns .btn {
            font-size: 0.8em;
            padding: 5px 10px;
        }

        .no-users {
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
        #usersChart {
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

            #usersChart {
                height: 300px !important;
            }
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
        <h2>View Users</h2>

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
        if (count($users) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover" id="usersTable">';
            echo '<thead><tr><th>#</th><th>Username</th><th>Email</th><th>Created At</th><th>Status</th><th>Actions</th></tr></thead>';
            echo '<tbody>';

            $counter = 1;
            foreach ($users as $user) {
                $status = $user['is_active'] ? 'Active' : 'Inactive';
                $statusClass = $user['is_active'] ? 'btn-warning' : 'btn-success';
                $statusAction = $user['is_active'] ? 'Deactivate' : 'Activate';
                $statusIcon = $user['is_active'] ? 'bi-lock' : 'bi-unlock';

                echo '<tr>';
                echo '<td>' . $counter . '</td>';
                echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td class="action-btns">
                        <button class="btn ' . $statusClass . '" data-toggle="modal" data-target="#changeStatusModal" 
                                data-id="' . htmlspecialchars($user['user_id']) . '"
                                data-status="' . $statusAction . '"
                                data-icon="' . $statusIcon . '">
                            <i class="' . $statusIcon . '"></i> ' . $statusAction . '
                        </button>
                      </td>';
                echo '</tr>';
                $counter++;
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p class="no-users">No users available.</p>';
        }
        ?>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div id="pagination-info">Showing 1 to <?php echo min(10, count($users)); ?> of <?php echo count($users); ?> entries</div>
            <nav>
                <ul class="pagination" id="pagination">
                    <!-- Pagination items will be dynamically generated -->
                </ul>
            </nav>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <h4>User Registrations Over the Last 12 Months</h4>
            <canvas id="usersChart"></canvas>
        </div>
    </main>

    <!-- Modal for changing user status -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="statusForm" method="post" action="changestatus.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeStatusModalLabel">Change User Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="userId" name="user_id">
                        <input type="hidden" id="currentStatus" name="current_status">
                        <p>Are you sure you want to <span id="statusAction"></span> this user?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get the data from PHP
            const labels = <?= json_encode($months) ?>;
            const usersData = <?= json_encode($users_per_month) ?>;

            const ctx = document.getElementById('usersChart').getContext('2d');
            const usersChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Users',
                        data: usersData,
                        backgroundColor: 'rgba(23, 162, 184, 0.2)', // Blue color
                        borderColor: 'rgba(23, 162, 184, 1)', // Blue color
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4, // Smooth curves
                        pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(23, 162, 184, 1)',
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
        // Populate change status modal with user data
        $('#changeStatusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');
            var statusAction = button.data('status');
            var statusIcon = button.data('icon');

            // Set the values in the modal's form inputs
            var modal = $(this);
            modal.find('#userId').val(userId);
            modal.find('#currentStatus').val(statusAction);
            modal.find('#statusAction').text(statusAction);
            modal.find('#confirmStatusChange').html('<i class="' + statusIcon + '"></i> Confirm');
        });

        // Search functionality for filtering the table
        $(document).ready(function () {
            // Entries selection
            $('#entries').on('change', function () {
                const selected = parseInt($(this).val());
                $('#usersTable tbody tr').hide();
                $('#usersTable tbody tr').slice(0, selected).show();
                $('#pagination').empty();
                $('#pagination-info').text(`Showing 1 to ${Math.min(selected, countVisibleEntries())} of ${countVisibleEntries()} entries`);
            });

            // Initialize with default entries
            $('#entries').trigger('change');

            // Search filter
            $("#search").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#usersTable tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });

                // After filtering, update entries display
                const visibleEntries = countVisibleEntries();
                const selectedEntries = parseInt($('#entries').val());
                $('#usersTable tbody tr').hide();
                $('#usersTable tbody tr').slice(0, selectedEntries).show();
                $('#pagination').empty();
                $('#pagination-info').text(`Showing 1 to ${Math.min(selectedEntries, visibleEntries)} of ${visibleEntries} entries`);
            });

            // Function to count visible entries
            function countVisibleEntries() {
                return $('#usersTable tbody tr:visible').length;
            }
        });
    </script>
</body>

</html>
