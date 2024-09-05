<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Database connection
include '../db_connection.php';

// Check if stationId is provided
if (isset($_POST['stationId'])) {
    $stationId = $_POST['stationId'];

    // SQL query to delete the station
    $sql = "DELETE FROM stations WHERE station_id = :station_id";
    $stmt = $pdo->prepare($sql);

    // Execute query with bound parameters
    if ($stmt->execute([':station_id' => $stationId])) {
        // Redirect back to view stations with a success message
        header("Location: viewstation.php?deleted=1");
        exit;
    } else {
        // Redirect back to view stations with an error message
        header("Location: viewtation.php?error=1");
        exit;
    }
} else {
    // If accessed directly, redirect to the view stations page
    header("Location: viewstation.php");
    exit;
}
?>
