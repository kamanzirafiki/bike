<?php
include('header.php');
include '../db_connection.php'; // Ensure this file contains the PDO connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch the user's bookings from the database
    $stmt = $pdo->prepare("SELECT b.booking_id AS id, bs.bike_scooter_id, bs.name AS bike_scooter_name, ps.name AS pickup_station, ds.name AS dropoff_station, b.booking_date, b.total_price, b.status, bs.available
                           FROM bookings b
                           JOIN bikes_scooters bs ON b.bike_scooter_id = bs.bike_scooter_id
                           JOIN stations ps ON b.pickup_station = ps.station_id
                           JOIN stations ds ON b.dropoff_station = ds.station_id
                           WHERE b.user_id = :user_id
                           ORDER BY b.booking_date DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $bookings = []; // Set an empty array to prevent errors when using count($bookings)
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            width: 100px;
        }

        .status-Pending {
            background-color: #ffc107;
            color: #fff;
            font-weight: bold;
            padding: 0.2em 0.4em;
            border-radius: 20px;
            width: 20%;
            font-size: x-small;
            align-items: center;
        }

        .status-completed {
            background-color: #28a745;
            color: #fff;
            font-size: x-small;
            padding: 0.2em 0.4em;
            border-radius: 20px;
            width: 20%;
            align-items: center;
        }

        .status-canceled {
            background-color: #dc3545;
            color: #fff;
            font-size: x-small;
            padding: 0.2em 0.7em;
            border-radius: 20px;
            width: 30%;
            align-items: center;
        }

        .return-button {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .return-button:hover {
            background-color: #0056b3;
        }

        .modal-content {
            text-align: center;
        }

        .alert {
            display: none;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #000;
        }

        .navbar-brand {
            color: #fff;
        }

        .navbar-nav .nav-link {
            color: #fff;
            margin-right: 20px;
            padding: 10px 15px;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }

        .navbar-nav .nav-link:hover {
            background-color: #a4aba6;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav .nav-link.dropdown-toggle {
            padding: 10px 15px;
        }

        .navbar-nav .nav-link.dropdown-toggle:hover {
            background-color: transparent;
            color: #fff;
            box-shadow: none;
        }

        .hero-section {
            position: relative;
            background-image: url('../images/bike2.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            color: white;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 75%;
            transform: translate(-50%, -50%);
            text-align: left;
            z-index: 1;
        }

        .hero-content h3 {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .hero-content .btn {
            font-size: 16px;
            padding: 10px 20px;
            background-color: #a4aba6;
            border: none;
            border-radius: 50px;
            color: #fff;
        }

        .hero-content .btn:hover {
            background-color: #8f8989;
        }

        .form-control {
            width: 250px;
        }

        .dropdown-menu {
            background-color: black;
            color: #fff;
        }

        .dropdown-menu .dropdown-item {
            color: #fff;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #A4ABA6;
        }

        .dropdown-menu .dropdown-item:focus {
            background-color: #A4ABA6;
        }

        .navbar-brand i {
            position: absolute;
            left: -50px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 30px;
            animation: moveBike 10s linear infinite;
            z-index: 1000;
        }

        @keyframes moveBike {
            0% {
                left: -50px;
            }

            100% {
                left: 34%;
            }
        }
    </style>
</head>

<body>

    <main>
        <h2>My Bookings</h2>

        <!-- Success/Error Message Alert -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success" id="alertMessage">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($bookings) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Bike/Scooter</th>
                        <th>Pickup Station</th>
                        <th>Dropoff Station</th>
                        <th>Booking Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['bike_scooter_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['pickup_station']); ?></td>
                            <td><?php echo htmlspecialchars($booking['dropoff_station']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['total_price']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php if (strtolower($booking['status']) == 'pending'): ?>
                                        <i class="fa-solid fa-clock"></i>
                                    <?php elseif (strtolower($booking['status']) == 'completed'): ?>
                                        <i class="fa-sharp fa-solid fa-circle-check"></i>
                                    <?php elseif (strtolower($booking['status']) == 'canceled'): ?>
                                        <i class="fa-solid fa-ban"></i>
                                    <?php endif; ?>
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strtolower($booking['available']) == 'booked'): ?>
                                    <button class="return-button" onclick="returnVehicle(<?php echo $booking['bike_scooter_id']; ?>)">Return Vehicle</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no bookings.</p>
        <?php endif; ?>
    </main>

    <!-- Modal for payment confirmation -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pending Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You have a pending booking. Please choose an action.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="proceedPaymentBtn">Proceed to Pay</button>
                    <button type="button" class="btn btn-danger" id="cancelBookingBtn">Cancel Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let pendingBookingId = null;

        document.addEventListener("DOMContentLoaded", function() {
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php if (strtolower($booking['status']) == 'pending'): ?>
                        pendingBookingId = <?php echo json_encode($booking['id']); ?>;
                        $('#paymentModal').modal('show');
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            // Handle Proceed to Payment action
            $("#proceedPaymentBtn").on("click", function() {
                if (pendingBookingId) {
                    window.location.href = "payment.php?booking_id=" + pendingBookingId;
                }
            });

            // Handle Cancel Booking action
            $("#cancelBookingBtn").on("click", function() {
                if (pendingBookingId) {
                    window.location.href = "cancel_booking.php?booking_id=" + pendingBookingId;
                }
            });

            // Show alert message for 5 seconds
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'block';
                setTimeout(function() {
                    alertMessage.style.display = 'none';
                }, 5000);
            }
        });

        // Handle Return Vehicle action
        function returnVehicle(bikeScooterId) {
            $.post('return_vehicle.php', {
                bike_scooter_id: bikeScooterId
            }, function(response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            }, 'json');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/footer.php' ?>
</body>

</html>