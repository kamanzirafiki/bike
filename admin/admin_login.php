<?php
// Start the session
session_start();

include '../db_connection.php'; // Make sure this file contains your PDO connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the posted form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate form inputs
    if (empty($username) || empty($password)) {
        echo "Both fields are required.";
        exit;
    }

    try {
        // Query to fetch the admin details based on the username
        $sql = "SELECT admin_id, username, password FROM admin WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch the admin record
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the admin exists and the password matches (plain text comparison)
        if ($admin && $admin['password'] === $password) {
            // Store the admin ID and username in session variables
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['is_admin'] = true; // Use this to track admin session
            
            // Redirect to the admin dashboard or bookings page
            header("Location: index.php");
            exit;
        } else {
            // Invalid login attempt
            echo "Invalid username or password.";
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
