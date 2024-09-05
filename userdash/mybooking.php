<?php
include 'header.php';
include '../db_connection.php'; // Ensure this file contains the PDO connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch the user's bookings from the database
    $stmt = $pdo->prepare("SELECT b.booking_id AS id, bs.name AS bike_scooter_name, ps.name AS pickup_station, ds.name AS dropoff_station, b.booking_date, b.total_price, b.status
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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
    </style>
</head>
<body>

<main>
    <h2>My Bookings</h2>
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
                    <th>Actions</th>
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
                            <?php if (strtolower($booking['status']) == 'completed'): ?>
                                <form method="POST" action="return_vehicle.php">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Return Vehicle</button>
                                </form>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
