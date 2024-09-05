<?php
session_start();
require '../db_connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $pickup_station = isset($_POST['pickup_station']) ? trim($_POST['pickup_station']) : null;
    $dropoff_station = isset($_POST['dropoff_station']) ? trim($_POST['dropoff_station']) : null;

    // Validate form data
    if (empty($pickup_station) || empty($dropoff_station)) {
        $_SESSION['message'] = 'All fields are required.';
        header('Location: route.php');
        exit;
    }

    try {
        // Check for existing route
        $query = "SELECT COUNT(*) FROM routes WHERE pickup_station = :pickup_station AND dropoff_station = :dropoff_station";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':pickup_station', $pickup_station);
        $stmt->bindParam(':dropoff_station', $dropoff_station);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $_SESSION['message'] = 'Route already exists.';
        } else {
            // Insert into database
            $query = "INSERT INTO routes (pickup_station, dropoff_station, created_at, updated_at) VALUES (:pickup_station, :dropoff_station, NOW(), NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':pickup_station', $pickup_station);
            $stmt->bindParam(':dropoff_station', $dropoff_station);
            $stmt->execute();

            // Success message
            $_SESSION['message'] = 'Route added successfully.';
        }
    } catch (PDOException $e) {
        // Error message
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }

    // Redirect
    header('Location: route.php');
    exit;
}
