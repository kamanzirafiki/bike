<?php


include '../db_connection.php';

if (isset($_GET['id'])) {
    $bike_scooter_id = $_GET['id'];

    // Prepare and execute the update query
    $sql = "UPDATE bikes_scooters SET is_active = TRUE WHERE bike_scooter_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $bike_scooter_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: view_vehicles.php?status=reactivated');
    } else {
        echo "Error reactivating vehicle.";
    }
} else {
    echo "No vehicle ID provided.";
}
?>
