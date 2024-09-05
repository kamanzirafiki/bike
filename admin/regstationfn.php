<?php
// Include the database connection file
include '../db_connection.php';
session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);

    // Validate required fields
    if (empty($name) || empty($address)) {
        $_SESSION['message'] = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please provide both station name and address.</div>';
        header("Location: regstation.php");
        exit();
    }

    try {
        // Check if a station with the same name already exists
        $check_sql = "SELECT * FROM stations WHERE name = :name";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            $_SESSION['message'] = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> A station with the same name already exists. Please use a different name.</div>';
        } else {
            // Insert the new station into the database
            $sql = "INSERT INTO stations (name, address) VALUES (:name, :address)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['message'] = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Station registered successfully!</div>';
            } else {
                $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Failed to register the station. Please try again later.</div>';
            }
        }
    } catch (PDOException $e) {
        // Capture and display database-related errors
        $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    // Redirect back to the station registration page
    header("Location: regstation.php");
    exit();
} else {
    // Redirect to the registration page if the request method is not POST
    header("Location: regstation.php");
    exit();
}
?>
