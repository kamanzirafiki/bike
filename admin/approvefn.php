<?php
// Include database connection
include '../db_connection.php';

// Check if the necessary parameters are set
if (isset($_GET['id']) && isset($_GET['action'])) {
    $bike_scooter_id = $_GET['id'];
    $action = $_GET['action'];

    // Validate parameters
    if (!in_array($action, ['approve', 'reject'])) {
        die('Invalid action');
    }

    // Prepare the SQL statement
    if ($action === 'approve') {
        $sql = "UPDATE bikes_scooters SET approval_status = 'approved' WHERE bike_scooter_id = :id";
    } elseif ($action === 'reject') {
        $sql = "UPDATE bikes_scooters SET approval_status = 'rejected' WHERE bike_scooter_id = :id";
    }

    try {
        // Prepare and execute the statement
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $bike_scooter_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect with success message
        header('Location: approve.php?message=success');
    } catch (PDOException $e) {
        // Handle error and redirect with error message
        header('Location: approve.php?message=error');
    }
} else {
    // Redirect with error message if parameters are missing
    header('Location: approve.php?message=error');
}
exit;
?>
