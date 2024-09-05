<?php

include '../db_connection.php';
include 'header.php';

// Check if booking_id is provided
if (!isset($_GET['booking_id'])) {
    echo "No booking ID provided.";
    exit;
}

$booking_id = $_GET['booking_id'];

try {
    // Fetch the booking details
    $sql = "SELECT 
                bookings.booking_id,
                bookings.booking_date,
                bookings.total_price,
                bookings.status,
                users.username AS user_name,
                users.user_id AS user_id,
                users.email AS user_email,
                bikes_scooters.name AS vehicle_name,
                pickup_station.name AS pickup_station_name,
                dropoff_station.name AS dropoff_station_name
            FROM bookings
            JOIN users ON bookings.user_id = users.user_id
            JOIN bikes_scooters ON bookings.bike_scooter_id = bikes_scooters.bike_scooter_id
            JOIN stations AS pickup_station ON bookings.pickup_station = pickup_station.station_id
            JOIN stations AS dropoff_station ON bookings.dropoff_station = dropoff_station.station_id
            WHERE bookings.booking_id = :booking_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo "Booking not found.";
        exit;
    } else {
        if ($booking['status'] == 'Completed') {
            Header("Location: userdash/my_booking.php");
            exit;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Initiate payment
// Capture booking details
$user_id = $booking['user_id'];
$booking_id = $booking['booking_id'];
$tx_ref = 'txref-' . uniqid();

// Check if there are any unpaid or pending payments for this booking
$sql_check = "SELECT * FROM payments WHERE booking_id = :booking_id AND status IN ('pending', 'unpaid')";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([':booking_id' => $booking_id]);

if ($stmt_check->rowCount() > 0) {
    
} else {
    
    $sql = "INSERT INTO payments (user_id, tx_ref, status, transaction_id, booking_id) VALUES (:user_id, :tx_ref, 'pending', '0', :booking_id)";
     
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':tx_ref' => $tx_ref,
        ':booking_id' => $booking_id
    ]);
}     
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Booking Summary</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: url('../images/bike2.jpg') no-repeat center center/cover;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .summary-card {
            border:none;
            width: 40%;
            background-color: rgba(255, 255, 255, 0.9); /* Add transparency */
            color: #333;
            border-radius: .5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Softer shadow */
            margin: 100px auto; /* Centered on the page */
            padding: 20px; /* Add padding for content */
            position: relative;
        }

        .summary-card-header {
            color: #444;
            font-weight: bold;
            font-size: 1.5rem;
            padding-bottom: .5rem;
            text-align: center; /* Center the header */
        }

        .summary-card-body {
            padding: 1.5rem;
            color: #333;
        }

        .summary-card-body p {
            font-size: 1rem;
            margin-bottom: .5rem;
            color: #555;
        }

        .btn-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px 0;
        }

        .btn-wrap button {
            padding: 0 32px;
            font-size: 18px;
            line-height: 48px;
            border: 1px solid transparent;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.5s ease;
            background-color: #A4ABA6; /* Green button */
            cursor: pointer;
            box-shadow: 0 0 5px 5px #00000020;
            color: #fff;
        }

        .btn-wrap button:hover {
            border: 1px solid #A4ABA6;
            background: transparent;
            color: #A4ABA6;
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
            background-image: url('images/bike2.jpg');
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
            background-color: black ;
            color: #fff;
        }
        .dropdown-menu .dropdown-item {
            color: #fff;
        }
        .dropdown-menu .dropdown-item:hover {
            background-color: #A4ABA6;
        }
        .dropdown-menu .dropdown-item:focus {
            background-color: #A4ABA6; /* Ensure it stays the same color when focused */
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="summary-card">
            <div class="summary-card-header">
                <h4>Booking Summary</h4>
            </div>
            <div class="summary-card-body">
                <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                <p><strong>User:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
                <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($booking['vehicle_name']); ?></p>
                <p><strong>Pickup Station:</strong> <?php echo htmlspecialchars($booking['pickup_station_name']); ?></p>
                <p><strong>Dropoff Station:</strong> <?php echo htmlspecialchars($booking['dropoff_station_name']); ?></p>
                <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                <p><strong>Total Price:</strong> <?php echo htmlspecialchars($booking['total_price']); ?> RWF</p>

                <form method="POST" action="https://checkout.flutterwave.com/v3/hosted/pay">
                    <input type="hidden" name="public_key" value="FLWPUBK_TEST-ced18b3be79e7fcd957ca431a8da2796-X" />
                    <input type="hidden" name="customer[email]" value="<?= htmlspecialchars($booking['user_email']); ?>" />
                    <input type="hidden" name="customer[name]" value="<?= htmlspecialchars($booking['user_name']); ?>" />
                    <input type="hidden" name="tx_ref" value="<?= $tx_ref; ?>" />
                    <input type="hidden" name="amount" value="<?= htmlspecialchars($booking['total_price']); ?>" />
                    <input type="hidden" name="currency" value="RWF" />
                    <input type="hidden" name="redirect_url" value="https://5831-197-243-40-122.ngrok-free.app/bike_scooters/userdash/processPayment.php?booking_id=<?= htmlspecialchars($booking['booking_id']); ?>" />
                    <input type="hidden" name="meta[source]" value="docs-html-test" />
                    <br>
                    <div class="btn-wrap">
                        <button type="submit" id="start-payment-button">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include '../includes/footer.php';?>
</body>

</html>
