<?php
// book.php

// Include the database connection (ensure this file initializes the $pdo object)
include '../db_connection.php';

// Start the session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to make a booking.";
    exit;
}

// Check if all required POST data is provided 
if (!isset($_POST['bike_scooter_id'], $_POST['pickup_station'], $_POST['dropoff_station'], $_POST['total_price'])) {
    echo "Missing required information.";
    exit;
}

// Validate and sanitize input data
$bike_scooter_id = filter_var($_POST['bike_scooter_id'], FILTER_VALIDATE_INT);
$pickup_station = filter_var($_POST['pickup_station'], FILTER_VALIDATE_INT);
$dropoff_station = filter_var($_POST['dropoff_station'], FILTER_VALIDATE_INT);
$total_price = filter_var($_POST['total_price'], FILTER_VALIDATE_FLOAT);
$user_id = $_SESSION['user_id'];
$booking_date = date('Y-m-d H:i:s');
$status = 'pending'; // You can use constants or enums for status values

// Check for valid input data
if ($bike_scooter_id === false || $pickup_station === false || $dropoff_station === false || $total_price === false) {
    echo "Invalid input data.";
    exit;
}

try {
    // Begin a transaction to ensure atomicity
    $pdo->beginTransaction();

    // Step 1: Check if the selected bike/scooter is still available
    $availability_sql = "SELECT available FROM bikes_scooters WHERE bike_scooter_id = :bike_scooter_id FOR UPDATE";
    $availability_stmt = $pdo->prepare($availability_sql);
    $availability_stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $availability_stmt->execute();
    $vehicle = $availability_stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging output (optional)
    // error_log("Checking availability for vehicle ID: " . $bike_scooter_id);
    // error_log("Vehicle data: " . print_r($vehicle, true));

    if (!$vehicle) {
        // Vehicle does not exist
        throw new Exception("Selected vehicle does not exist.");
    }

    if ($vehicle['available'] !== 'returned') {
        // Vehicle is not available for booking
        throw new Exception("Sorry, the selected vehicle is no longer available.");
    }

    // Step 2: Insert the booking into the bookings table
    $insert_booking_sql = "
        INSERT INTO bookings (
            bike_scooter_id, 
            pickup_station, 
            dropoff_station, 
            booking_date, 
            total_price, 
            status, 
            user_id
        ) VALUES (
            :bike_scooter_id, 
            :pickup_station, 
            :dropoff_station, 
            :booking_date, 
            :total_price, 
            :status, 
            :user_id
        )
    ";
    $insert_booking_stmt = $pdo->prepare($insert_booking_sql);
    $insert_booking_stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $insert_booking_stmt->bindParam(':pickup_station', $pickup_station, PDO::PARAM_INT);
    $insert_booking_stmt->bindParam(':dropoff_station', $dropoff_station, PDO::PARAM_INT);
    $insert_booking_stmt->bindParam(':booking_date', $booking_date, PDO::PARAM_STR);
    $insert_booking_stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
    $insert_booking_stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $insert_booking_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    $insert_booking_stmt->execute();

    // Retrieve the last inserted booking ID
    $booking_id = $pdo->lastInsertId();

    // Step 3: Update the availability of the booked vehicle to 'booked'
    $update_vehicle_sql = "UPDATE bikes_scooters SET available = 'booked' WHERE bike_scooter_id = :bike_scooter_id";
    $update_vehicle_stmt = $pdo->prepare($update_vehicle_sql);
    $update_vehicle_stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $update_vehicle_stmt->execute();

    // Commit the transaction as all operations were successful
    $pdo->commit();

    // Redirect to the payment page with the booking ID
    header("Location: payment.php?booking_id=" . urlencode($booking_id));
    exit;

} catch (Exception $e) {
    // Rollback the transaction in case of any errors
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log the error message to a file or monitoring system (optional but recommended)
    error_log("Booking Error: " . $e->getMessage());

    // Display a user-friendly error message
    echo "An error occurred while processing your booking: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
