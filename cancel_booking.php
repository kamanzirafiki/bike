<?php
include 'db_connection.php'; // Ensure this file contains the PDO connection
session_start();

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    try {
        // Fetch the bike/scooter ID associated with this booking
        $stmt = $pdo->prepare("SELECT bike_scooter_id FROM bookings WHERE booking_id = :booking_id");
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            // Get the bike/scooter ID
            $bike_scooter_id = $booking['bike_scooter_id'];

            // Begin a transaction to ensure data integrity
            $pdo->beginTransaction();

            // Cancel the booking by updating its status to "Canceled"
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'Canceled' WHERE booking_id = :booking_id");
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();

            // Check if booking update was successful
            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to update booking status.");
            }

            // Update the bike/scooter availability to 'available'
            $update_stmt = $pdo->prepare("UPDATE bikes_scooters SET available = 'available' WHERE bike_scooter_id = :bike_scooter_id");
            $update_stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
            $update_stmt->execute();

            // Check if vehicle update was successful
            if ($update_stmt->rowCount() === 0) {
                throw new Exception("Failed to update bike availability.");
            }

            // Commit the transaction
            $pdo->commit();

            // Redirect back to the bookings page with a success message
            $_SESSION['message'] = "Booking has been canceled and the bike/scooter is now available.";
            header("Location: ../userdash/mybooking.php");
            exit;
        } else {
            // Handle if booking doesn't exist
            $_SESSION['error'] = "Booking not found.";
            header("Location: ../userdash/mybooking.php");
            exit;
        }

    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        // Handle errors
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../userdash/mybooking.php");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../userdash/mybooking.php");
        exit;
    }
} else {
    // Redirect if no booking ID is provided
    header("Location: ../userdash/mybooking.php");
    exit;
}
?>
