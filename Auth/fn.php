<?php
session_start();
include '../db_connection.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['is_active'] == 1) { // Assuming 1 means active and 0 means inactive
                    // Set session and redirect
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: ../userdash/index.php");
                    exit();
                } else {
                    // User is deactivated
                    $_SESSION['error'] = "Your account has been deactivated. Please contact support.";
                    header("Location: login.php");
                    exit();
                }
            } else {
                // Invalid password
                $_SESSION['error'] = "Invalid email or password.";
                header("Location: login.php");
                exit();
            }
        } else {
            // User not found
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred. Please try again.";
        header("Location: login.php");
        exit();
    }
}
?>
