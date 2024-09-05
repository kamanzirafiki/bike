<?php
include '../db_connection.php'; // Ensure this file contains the PDO connection

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Validate the booking ID
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = :booking_id AND user_id = :user_id");
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the booking exists
        if ($stmt->rowCount() === 0) {
            echo "Invalid booking ID.";
            exit;
        }

        // Update the booking status to 'Returned'
        $updateStmt = $pdo->prepare("UPDATE bookings SET status = 'Completed' WHERE booking_id = :booking_id");
        $updateStmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Optionally, update the status of the bike/scooter to 'returned'
        $updateBikeStmt = $pdo->prepare("UPDATE bikes_scooters SET available = 'returned' WHERE bike_scooter_id = (SELECT bike_scooter_id FROM bookings WHERE booking_id = :booking_id)");
        $updateBikeStmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $updateBikeStmt->execute();

        // Redirect back to bookings page with a success message
        header("Location: mybooking.php?success=Vehicle returned successfully.");
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
