<?php
// Include database connection
include '../db_connection.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Retrieve the vehicle ID from the URL
    $bike_scooter_id = $_GET['id'];

    try {
        // Prepare SQL statement to update the vehicle status to 'inactive'
        $sql = "UPDATE bikes_scooters SET approval_status = 'inactive' WHERE bike_scooter_id = :bike_scooter_id";
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameter and execute the statement
        $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to the view vehicles page with a success message
        header("Location: viewvehicles.php?message=Vehicle successfully deactivated.");
        exit();
    } catch (PDOException $e) {
        // Redirect to the view vehicles page with an error message
        header("Location: viewvehicles.php?error=Error deactivating vehicle: " . htmlspecialchars($e->getMessage()));
        exit();
    }
} else {
    // Redirect to the view vehicles page with an error message if no ID is provided
    header("Location: viewvehicles.php?error=Invalid vehicle ID.");
    exit();
}
?>
