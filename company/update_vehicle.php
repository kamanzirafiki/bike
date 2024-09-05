<?php
session_start();
include '../db_connection.php'; // Ensure you have this file for DB connection

if (!isset($_SESSION['vendor_id'])) {
    die("Vendor not logged in.");
}

// Fetch vehicle types from the database
$sql = "SELECT DISTINCT type FROM bikes_scooters";
$stmt = $pdo->query($sql);
$vehicle_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $name = $_POST['name'];
    $model = $_POST['model'];
    $details = $_POST['details'];
    $type = $_POST['type']; // Get type from the form submission

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type and size if needed
        // Add your file type and size checks here...

        // Upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File upload success
        } else {
            // File upload error
            $image_name = null; // Set image name to null if upload fails
        }
    } else {
        // Use the old image if no new image is uploaded
        $image_name = $_POST['current_image'] ?? null;
    }

    // Update vehicle details in the database
    $sql = "UPDATE bikes_scooters SET name = ?, model = ?, details = ?, type = ?, image = ? WHERE bike_scooter_id = ? AND vendor_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $model, $details, $type, $image_name, $vehicle_id, $_SESSION['vendor_id']]);

    // Redirect back with success status
    header("Location: viewvehicle.php?status=success");
    exit;
}
