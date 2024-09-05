<?php
session_start();
include '../db_connection.php'; // Include your database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Get the action and ID from the URL
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        // Prepare and execute the query to block or unblock the vehicle
        if ($action === 'block') {
            $sql = "UPDATE bikes_scooters SET available = 'blocked' WHERE bike_scooter_id = :id";
        } elseif ($action === 'unblock') {
            $sql = "UPDATE bikes_scooters SET available = 'returned' WHERE bike_scooter_id = :id"; // or 'available' based on your logic
        } else {
            $_SESSION['message'] = "Invalid action.";
            $_SESSION['message_type'] = "danger";
            header("Location: viewvehicle.php");
            exit;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Set success message
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Vehicle successfully " . ($action === 'block' ? 'blocked' : 'unblocked') . ".";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: No vehicle found with that ID.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Invalid vehicle ID.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to the vehicle view page
header("Location: viewvehicle.php");
exit;
?>
