<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Database connection
require '../db_connection.php';

// Check if the form is submitted and the ID is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $subscriberId = $_POST['id'];

    try {
        // Prepare the DELETE statement
        $stmt = $pdo->prepare("DELETE FROM subscribers WHERE id = :id");
        $stmt->bindParam(':id', $subscriberId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to viewsubs.php with a success message
        header("Location: viewsubs.php?message=Subscriber+deleted+successfully");
        exit;

    } catch (PDOException $e) {
        // Redirect to viewsubs.php with an error message
        header("Location: viewsubs.php?error=Failed+to+delete+subscriber");
        exit;
    }
} else {
    // If ID is not set, redirect to viewsubs.php with an error message
    header("Location: viewsubs.php?error=Invalid+request");
    exit;
}
?>
