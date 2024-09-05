<?php
// Start the session (if not already started)
session_start();


include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the station details from the POST request
    $station_id = $_POST['station_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];

    // Prepare the SQL statement to update the station details
    $sql = "UPDATE stations SET name = :name, address = :address, updated_at = NOW() WHERE station_id = :station_id";
    
    try {
        // Prepare and execute the statement using PDO
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':station_id', $station_id);

        if ($stmt->execute()) {
            // Success message
            $_SESSION['success_message'] = "Station details updated successfully!";
        } else {
            // Error message
            $_SESSION['error_message'] = "Failed to update station details.";
        }
    } catch (PDOException $e) {
        // Handle the error
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to the view stations page after the update
header("Location: viewstation.php");
exit();
