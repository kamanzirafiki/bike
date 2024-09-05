<?php
include '../db_connection.php'; // Ensure this file contains the PDO connection
session_start(); // Assuming admin login is handled using sessions

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You are not authorized to view this page.";
    exit;
}

try {
    // Fetch all bookings along with associated user, vehicle, and route details
    $sql = "SELECT 
                bookings.booking_id,
                bookings.booking_date,
                bookings.total_price,
                bookings.status,
                users.username AS user_name,
                bikes_scooters.name AS vehicle_name,
                routes.pickup_station,
                routes.dropoff_station
            FROM bookings
            JOIN users ON bookings.user_id = users.user_id
            JOIN bikes_scooters ON bookings.bike_scooter_id = bikes_scooters.bike_scooter_id
            JOIN routes ON bookings.route_id = routes.route_id
            ORDER BY bookings.booking_date DESC";
    
    $stmt = $pdo->query($sql);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    <title>Admin - Bookings Overview</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>All Bookings</h2>
        <table class="table table-bordered table-striped mt-4">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)) : ?>
                    <?php foreach ($bookings as $booking) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['vehicle_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['pickup_station']); ?></td>
                            <td><?php echo htmlspecialchars($booking['dropoff_station']); ?></td>
                            <td><?php echo htmlspecialchars($booking['total_price']); ?> USD</td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td>
                                <a href="admin_view_booking.php?booking_id=<?php echo htmlspecialchars($booking['booking_id']); ?>" class="btn btn-info btn-sm">View</a>
                                <a href="admin_update_booking.php?booking_id=<?php echo htmlspecialchars($booking['booking_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
