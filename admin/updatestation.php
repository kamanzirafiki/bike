<?php
include '../db_connection.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stationId = $_POST['stationId'];
    $stationName = $_POST['stationName'];
    $stationAddress = $_POST['stationAddress'];

    // SQL query to update the station's data
    $sql = "UPDATE stations SET name = :name, address = :address WHERE station_id = :station_id";
    $stmt = $pdo->prepare($sql);

    // Execute query with bound parameters
    if ($stmt->execute([':name' => $stationName, ':address' => $stationAddress, ':station_id' => $stationId])) {
        // Redirect back to view stations with a success message
        header("Location: viewstation.php?success=1");
        exit;
    } else {
        // Redirect back to view stations with an error message
        header("Location: viewstation.php?error=1");
        exit;
    }
} else {
    // If accessed directly, redirect to the view stations page
    header("Location: viewstation.php");
    exit;
}
?>
