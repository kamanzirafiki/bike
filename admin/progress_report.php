<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Include database connection
include '../db_connection.php'; // assuming db_connection.php has your PDO connection

$year = date('Y');
$month = date('m');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'] ?? date('Y');
    $month = $_POST['month'] ?? date('m');
}

// Function to get data for the selected year
function getYearlyData($pdo, $year) {
    $data = [
        'bookings' => array_fill(1, 12, 0),
        'users' => array_fill(1, 12, 0),
        'vehicles' => array_fill(1, 12, 0),
        'subscribers' => array_fill(1, 12, 0),
        'vendors' => array_fill(1, 12, 0),
        'earnings' => array_fill(1, 12, 0),
    ];

    for ($m = 1; $m <= 12; $m++) {
        $month = str_pad($m, 2, '0', STR_PAD_LEFT);
        
        // Count bookings
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count, SUM(total_price) AS total_earnings FROM bookings WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?");
        $stmt->execute([$year, $month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['bookings'][$m] = $result['count'];
        $data['earnings'][$m] = $result['total_earnings'] * 0.20; // 20% of total earnings

        // Count users
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?");
        $stmt->execute([$year, $month]);
        $data['users'][$m] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count vehicles
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM bikes_scooters WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?");
        $stmt->execute([$year, $month]);
        $data['vehicles'][$m] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count subscribers
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM subscribers WHERE YEAR(subscribed_at) = ? AND MONTH(subscribed_at) = ?");
        $stmt->execute([$year, $month]);
        $data['subscribers'][$m] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count vendors
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM vendors WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?");
        $stmt->execute([$year, $month]);
        $data['vendors'][$m] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    return $data;
}

$yearlyData = getYearlyData($pdo, $year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Style adjustments for vertical charts */
        .chart-container {
            margin: 20px 0; /* Add some margin */
        }
        #progressChart, #earningsChart {
            width: 100%; /* Full width for charts */
            max-width: 600px; /* Limit the maximum width */
            margin: auto; /* Center the charts */
        }
        /* Reduce filter input size */
        .filter-input {
            width: 150px; /* Smaller width for inputs */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Progress Report for <?= $year ?></h2>
        <form method="POST" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <select name="month" class="form-control filter-input">
                        <option value="">All Months</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col">
                    <input type="text" name="year" class="form-control filter-input" value="<?= $year ?>" placeholder="Year" required />
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="chart-container">
            <canvas id="progressChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <script>
        // Chart for bookings, users, vehicles, subscribers, and vendors
        const ctxProgress = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctxProgress, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Month names
                datasets: [
                    {
                        label: 'Bookings',
                        data: <?= json_encode($yearlyData['bookings']) ?>,
                        borderColor: 'rgba(255, 107, 129, 1)',
                        fill: false,
                    },
                    {
                        label: 'Users',
                        data: <?= json_encode($yearlyData['users']) ?>,
                        borderColor: 'rgba(108, 92, 247, 1)',
                        fill: false,
                    },
                    {
                        label: 'Vehicles',
                        data: <?= json_encode($yearlyData['vehicles']) ?>,
                        borderColor: 'rgba(0, 206, 201, 1)',
                        fill: false,
                    },
                    {
                        label: 'Subscribers',
                        data: <?= json_encode($yearlyData['subscribers']) ?>,
                        borderColor: 'rgba(253, 203, 110, 1)',
                        fill: false,
                    },
                    {
                        label: 'Vendors',
                        data: <?= json_encode($yearlyData['vendors']) ?>,
                        borderColor: 'rgba(100, 221, 223, 1)',
                        fill: false,
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart for earnings only
        const ctxEarnings = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(ctxEarnings, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Month names
                datasets: [
                    {
                        label: 'Earnings',
                        data: <?= json_encode($yearlyData['earnings']) ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
