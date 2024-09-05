<?php
include '../db_connection.php';
session_start();

if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    
    try {
        // Update booking status to Approved
        $sql = "UPDATE bookings SET status = 'Approved' WHERE booking_id = :booking_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        
        header("Location: viewbooking.php");
        exit;
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: viewbooking.php");
    exit;
}
?>
