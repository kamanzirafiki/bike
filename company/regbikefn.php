<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit();
}

// Function to generate a unique file name for the image
function generateUniqueFileName($originalFileName) {
    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    return uniqid('img_', true) . '.' . $extension;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $type = htmlspecialchars($_POST['type']);
    $model = htmlspecialchars($_POST['model']);
    $plate_number = htmlspecialchars($_POST['plate_number']);
    $details = htmlspecialchars($_POST['details']);
    $vendor_id = intval($_SESSION['vendor_id']); // Retrieve vendor_id from the session

    // Handle image upload
    $image = $_FILES['image'];
    $imageName = generateUniqueFileName($image['name']);
    $targetDir = 'uploads/';
    $targetFile = $targetDir . $imageName;

    try {
        // Check if the plate number already exists in the database
        $checkSql = "SELECT COUNT(*) FROM bikes_scooters WHERE plate_number = :plate_number";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':plate_number', $plate_number);
        $checkStmt->execute();
        $existingPlateCount = $checkStmt->fetchColumn();

        if ($existingPlateCount > 0) {
            $_SESSION['message'] = "Error: A vehicle with this plate number already exists.";
            $_SESSION['message_type'] = "danger";
            header('Location: regbike.php'); // Redirect back to the form page
            exit();
        }

        // Validate image upload
        if ($image['error'] == UPLOAD_ERR_OK) {
            // Check if upload directory exists, if not, create it
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                // Prepare and execute the database insert
                $sql = "INSERT INTO bikes_scooters (vendor_id, name, model, plate_number, details, type, image, available, created_at, updated_at, approval_status)
                        VALUES (:vendor_id, :name, :model, :plate_number, :details, :type, :image, 'returned', NOW(), NOW(), 'pending')";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':model', $model);
                $stmt->bindParam(':plate_number', $plate_number);
                $stmt->bindParam(':details', $details);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':image', $imageName); // Store only the file name in the database

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Vehicle registered successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error: Unable to register bike/scooter.";
                    $_SESSION['message_type'] = "danger";
                }
            } else {
                $_SESSION['message'] = "Error uploading file.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "File upload error.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    header('Location: regbike.php'); // Redirect back to the form page
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header('Location: regbike.php');
    exit();
}
?>
