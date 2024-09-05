<?php
require 'db_connection.php'; // Assuming you have a PDO connection in this file


// Check if booking_id is provided
if (!isset($_GET['booking_id'])) {
    echo "No booking ID provided.";
    exit;
}
// Capture the data returned from Flutterwave after redirection
$booking_id = $_GET['booking_id']; // from the redirect URL
$status = $_GET['status']; // 'successful' or other
$tx_ref = $_GET['tx_ref'];
$transaction_id = $_GET['transaction_id'];

if ($status === 'successful') {
    // Update the payments table to set the payment as completed
    $sql_update_payment = "UPDATE payments SET status = 'Completed', transaction_id = :transaction_id WHERE booking_id = :booking_id";
    $stmt_update_payment = $pdo->prepare($sql_update_payment);
    $stmt_update_payment->execute([
        ':transaction_id' => $transaction_id,
        ':booking_id' => $booking_id
    ]);

    // Update the bookings table to mark the booking as paid
    $sql_update_booking = "UPDATE bookings SET status = 'Completed' WHERE booking_id = :booking_id";
    $stmt_update_booking = $pdo->prepare($sql_update_booking);
    $stmt_update_booking->execute([
        ':booking_id' => $booking_id
    ]);

    // Optional: Redirect the user to a success page or show a success message
    echo "Payment was successful. Booking has been updated.";
    header("location:index.php");
} else {
    // Handle cases where payment was not successful
    echo "Payment failed or was not successful. Please try again.";
}
?>
