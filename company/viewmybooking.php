<?php
session_start();

// Check if the vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

// Get the vendor ID from the session
$vendor_id = $_SESSION['vendor_id'];

// Database connection
include '../db_connection.php';

// Prepare and execute the query using PDO
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
        WHERE bikes_scooters.vendor_id = :vendor_id
        ORDER BY bookings.booking_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            margin-left: 260px;
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
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .no-bookings {
            text-align: center;
            padding: 20px;
            font-size: 1em;
            color: #555;
        }

        
        .status-Pending,
        .status-Completed,
        .status-Canceled {
            display: inline-flex;
            align-items: center;
            padding: 0.3em 0.7em;
            border-radius: 20px;
            font-size: 0.6em;
            font-weight: bold;
            color: #fff;
           
        }

    
        .status-Pending {
            background-color: #ffc107;
            /* Yellow background */
        }

        /* Completed Status */
        .status-Completed {
            background-color: #28a745;
            /* Green background */
        }

        /* Canceled Status */
        .status-Canceled {
            background-color: #dc3545;
            /* Red background */
        }

        /* Icon Styles */
        .status-icon {
            margin-right: 0.5em;
            font-size: 1.2em;
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

        /* Custom Print Button Styling */
        .custom-print-btn {
            background-color: #007bff;
            /* Bootstrap primary blue */
            color: white;
            padding: 5px 10px;
            font-size: 0.875rem;
            /* Smaller font size */
            border: none;
            border-radius: 4px;
            height: 35px;
            /* Reduced height */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .custom-print-btn:hover {
            background-color: #0069d9;
            /* Darker blue on hover */
        }

        /* Print-specific styles */
        @media print {
            body {
                background-color: white;
            }

            /* Hide elements not needed for printing */
            .search-bar,
            .custom-print-btn,
            .btn,
            .modal,
            .navbar,
            .sidebar,
            .overlay {
                display: none !important;
            }

            main {
                margin-left: 0;
                padding: 0;
            }

            table {
                border: 1px solid #000;
            }

            th,
            td {
                border: 1px solid #000;
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

    <main>
        <h2>My Bookings</h2>

        <!-- Search bar, Entries dropdown, and Print button -->
        <div class="search-bar">
            <div class="form-group d-flex align-items-center">
                <label for="entries" class="mr-2 mb-0">Show</label>
                <select id="entries" class="form-control mr-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="search" class="mr-2 mb-0">Search:</label>
                <input type="text" id="search" class="form-control mr-4" placeholder="Search bookings...">
                <button class="custom-print-btn" id="printButton"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <!-- Display Bookings -->
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Booking Date</th>
                            <th>User</th>
                            <th>Vehicle</th>
                            <th>Pickup Station</th>
                            <th>Dropoff Station</th>
                            <th>Total Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['vehicle_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['pickup_station_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['dropoff_station_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['total_price']); ?> RWF</td>
                                <td>
                                    <?php
                                    // Define status class and icon based on the booking status
                                    $statusClass = '';
                                    $statusIcon = '';

                                    switch (htmlspecialchars($booking['status'])) {
                                        case 'Pending':
                                            $statusClass = 'status-Pending';
                                            $statusIcon = '<i class="fa-solid fa-clock status-icon"></i>'; // FontAwesome clock icon
                                            break;
                                        case 'Completed':
                                            $statusClass = 'status-Completed';
                                            $statusIcon = '<i class="fa-sharp fa-solid fa-circle-check status-icon"></i>'; // FontAwesome check icon
                                            break;
                                        case 'Canceled':
                                            $statusClass = 'status-Canceled';
                                            $statusIcon = '<i class="fa-solid fa-ban status-icon"></i>'; // FontAwesome ban icon
                                            break;
                                    }
                                    ?>
                                    <!-- Display the status with its respective icon and text -->
                                    <span class="<?php echo $statusClass; ?>">
                                        <?php echo $statusIcon; ?>
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-bookings">No bookings found for your vehicles.</p>
        <?php endif; ?>
    </main>

    <!-- Overlay (if needed for modals or other features) -->
    <div class="overlay" id="overlay"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Search functionality
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#bookingsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Entries filtering functionality
            $('#entries').on('change', function() {
                var value = $(this).val();
                var rows = $('#bookingsTable tbody tr');
                rows.show();
                if (value !== 'All') {
                    rows.slice(value).hide();
                }
            });

            // Trigger the change event on page load to set the initial number of entries
            $('#entries').trigger('change');

            // Print functionality
            $('#printButton').on('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>