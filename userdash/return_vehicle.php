<?php
include '../db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bike_scooter_id = $_POST['bike_scooter_id'];

    try {
        // Update the bike/scooter's available status to 'returned'
        $stmt = $pdo->prepare("UPDATE bikes_scooters SET available = 'returned' WHERE bike_scooter_id = :bike_scooter_id");
        $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Vehicle returned successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
