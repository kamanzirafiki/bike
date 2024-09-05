<?php
include '../db_connection.php'; // Ensure this file contains the PDO connection

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    try {
        // Cancel the booking by updating its status to "Canceled"
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'Canceled' WHERE booking_id = :booking_id");
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the bookings page with a success message
        $_SESSION['message'] = "Booking has been canceled successfully.";
        header("Location: mybooking.php");
        exit;

    } catch (PDOException $e) {
        // Handle errors
        $_SESSION['error'] = "Failed to cancel the booking.";
        header("Location: mybooking.php");
        exit;
    }
} else {
    // Redirect if no booking ID is provided
    header("Location:booking.php");
    exit;
}
?>
