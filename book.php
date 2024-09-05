<?php
include 'db_connection.php'; // Ensure this file contains the PDO connection
session_start(); // Assuming you're using sessions to handle logged-in users

// Check if all required POST data is provided
if (!isset($_POST['bike_scooter_id'], $_POST['pickup_station'], $_POST['dropoff_station'], $_POST['booking_date'], $_POST['total_price'], $_POST['user_id'])) {
    echo "Missing required information.";
    exit;
}

$bike_scooter_id = $_POST['bike_scooter_id'];
$pickup_station = $_POST['pickup_station'];
$dropoff_station = $_POST['dropoff_station'];
$booking_date = $_POST['booking_date'];
$total_price = $_POST['total_price'];
$user_id = $_POST['user_id']; // User ID passed from session
$status = 'pending'; // Initial booking status

try {
    // Check if a route exists for the selected pickup and dropoff stations
    $route_sql = "SELECT route_id FROM routes WHERE pickup_station = :pickup_station AND dropoff_station = :dropoff_station LIMIT 1";
    $route_stmt = $pdo->prepare($route_sql);
    $route_stmt->bindParam(':pickup_station', $pickup_station, PDO::PARAM_STR);
    $route_stmt->bindParam(':dropoff_station', $dropoff_station, PDO::PARAM_STR);
    $route_stmt->execute();
    $route = $route_stmt->fetch();

    if (!$route) {
        echo "No valid route found for the selected pickup and dropoff stations.";
        exit;
    }

    $route_id = $route['route_id'];

    // Insert the booking into the bookings table
    $sql = "INSERT INTO bookings (bike_scooter_id, pickup_station, dropoff_station, booking_date, total_price, status, user_id, route_id)
            VALUES (:bike_scooter_id, :pickup_station, :dropoff_station, :booking_date, :total_price, :status, :user_id, :route_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $stmt->bindParam(':pickup_station', $pickup_station, PDO::PARAM_STR);
    $stmt->bindParam(':dropoff_station', $dropoff_station, PDO::PARAM_STR);
    $stmt->bindParam(':booking_date', $booking_date, PDO::PARAM_STR);
    $stmt->bindParam(':total_price', $total_price, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':route_id', $route_id, PDO::PARAM_INT);

    $stmt->execute();

    // Retrieve the last inserted booking ID
    $booking_id = $pdo->lastInsertId();

    // Redirect to payment page
    header("Location: payment.php?booking_id=$booking_id");
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
