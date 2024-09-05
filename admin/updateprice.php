<?php
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $price = $_POST['price'];

    $sql = "UPDATE bikes_scooters SET price = :price WHERE bike_scooter_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['price' => $price, 'id' => $id]);

    // Redirect to the view vehicles page with a success message
    header("Location: viewvehicle.php?success=1");
    exit();
}
?>
