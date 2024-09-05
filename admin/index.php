<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Include database connection
include '../db_connection.php'; // assuming db_connection.php has your PDO connection

// Fetch counts from database
try {
    $bookings_stmt = $pdo->query("SELECT COUNT(*) AS count FROM bookings"); 
    $bookings_count = $bookings_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $users_stmt = $pdo->query("SELECT COUNT(*) AS count FROM users"); 
    $users_count = $users_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $vehicles_stmt = $pdo->query("SELECT COUNT(*) AS count FROM bikes_scooters"); 
    $vehicles_count = $vehicles_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $vendors_stmt = $pdo->query("SELECT COUNT(*) AS count FROM vendors"); 
    $vendors_count = $vendors_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Calculate total earnings
    $earnings_stmt = $pdo->query("SELECT SUM(total_price) AS total_earnings FROM bookings");
    $total_earnings = $earnings_stmt->fetch(PDO::FETCH_ASSOC)['total_earnings'] ?? 0;
    $earnings = $total_earnings * 0.20; // Calculate 20% of total earnings

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            margin: 5px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 200px; 
            height: 100px; 
            position: relative;
        }

        .card-body {
            padding: 10px; 
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-icon {
            font-size: 1.5rem; 
            position: absolute;
            top: 5px;
            right: 10px;
            color: white;
            padding: 5px; 
            border-radius: 50%;
        }

        .icon-bg-bookings { background-color: #ff6b81; }
        .icon-bg-users { background-color: #6c5ce7; }
        .icon-bg-vehicles { background-color: #00cec9; }
        .icon-bg-earnings { background-color: #fdcb6e; } /* New icon background */

        .stat-number { font-size: 1.2rem; font-weight: bold; }
        .stat-text { font-size: 0.8rem; font-weight: normal; color: #333; }
        .view-details { font-size: 0.75rem; color: #007bff; text-decoration: none; font-weight: bold; margin-top: 2px; }

        .dashboard-stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; 
        }

        
        .chart-container {
            margin: 20px 0;
        }

        
        @media (max-width: 768px) {
            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
                <?php include 'header.php'; ?>
                <div class="container-fluid mt-5">
                    <h3>Admin Dashboard</h3>

                    <div class="dashboard-stats">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-text">Bookings</div>
                                <div class="stat-number"><?= $bookings_count ?></div>
                                <a href="viewbooking.php" class="view-details">View Details</a>
                                <div class="stat-icon icon-bg-bookings">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="stat-text">Users</div>
                                <div class="stat-number"><?= $users_count ?></div>
                                <a href="viewUser.php" class="view-details">View Details</a>
                                <div class="stat-icon icon-bg-users">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="stat-text">Vehicles</div>
                                <div class="stat-number"><?= $vehicles_count ?></div>
                                <a href="viewvehicle.php" class="view-details">View Details</a>
                                <div class="stat-icon icon-bg-vehicles">
                                    <i class="fas fa-car"></i>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="stat-text">Earnings</div>
                                <div class="stat-number"><?= number_format($earnings, 2) ?> RWF</div> <!-- Display earnings formatted to 2 decimal places -->
                                <a href="viewEarnings.php" class="view-details">View Details</a> <!-- Link for earnings -->
                                <div class="stat-icon icon-bg-earnings">
                                    <i class="fas fa-money-bill-wave"></i> <!-- Changed to a different earnings icon -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="stat-text">Vendors</div>
                                <div class="stat-number"><?= $vendors_count ?></div>
                                <a href="viewvendors.php" class="view-details">View Details</a>
                                <div class="stat-icon icon-bg-vendors">
                                    <i class="fas fa-truck"></i>
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="chart-container">
                        <canvas id="dashboardChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    const dashboardChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Bookings', 'Users', 'Vehicles', 'Vendors'], // Updated labels (removed Earnings)
            datasets: [{
                label: 'Counts',
                data: [<?= $bookings_count ?>, <?= $users_count ?>, <?= $vehicles_count ?>, <?= $vendors_count ?>], // Updated data
                backgroundColor: [
                    'rgba(255, 107, 129, 0.6)', 
                    'rgba(108, 92, 247, 0.6)', 
                    'rgba(0, 206, 201, 0.6)', 
                    'rgba(232, 67, 147, 0.6)' 
                ],
                borderColor: [
                    'rgba(255, 107, 129, 1)',
                    'rgba(108, 92, 247, 1)',
                    'rgba(0, 206, 201, 1)',
                    'rgba(232, 67, 147, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    grid: {
                        display: false 
                    }
                }
            },
            barThickness: 60, 
        }
    });
</script>

</body>
</html>
