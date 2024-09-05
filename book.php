<?php
include 'db_connection.php'; // Ensure this file contains the PDO connection
session_start(); // Assuming you're using sessions to handle logged-in users

// Check if all required POST data is provided 
if (!isset($_POST['bike_scooter_id'], $_POST['pickup_station'], $_POST['dropoff_station'], $_POST['booking_date'], $_POST['total_price'])) {
    echo "Missing required information.";
    exit;
}

$bike_scooter_id = $_POST['bike_scooter_id'];
$pickup_station = $_POST['pickup_station']; // Assuming this is the station ID
$dropoff_station = $_POST['dropoff_station']; // Assuming this is the station ID
$booking_date = $_POST['booking_date'];
$total_price = $_POST['total_price'];
$user_id = $_SESSION['user_id']; // User ID passed from session
$status = 'pending'; // Initial booking status

try {
    // Insert the booking into the bookings table
    $sql = "INSERT INTO bookings (bike_scooter_id, pickup_station, dropoff_station, booking_date, total_price, status, user_id)
            VALUES (:bike_scooter_id, :pickup_station, :dropoff_station, :booking_date, :total_price, :status, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $stmt->bindParam(':pickup_station', $pickup_station, PDO::PARAM_INT);
    $stmt->bindParam(':dropoff_station', $dropoff_station, PDO::PARAM_INT);
    $stmt->bindParam(':booking_date', $booking_date, PDO::PARAM_STR);
    $stmt->bindParam(':total_price', $total_price, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

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
