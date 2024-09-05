<?php
include '../db_connection.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user input from form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Initialize message variable
    $message = "";
    $msg_type = "danger"; // Default message type

    // Validate inputs
    if (empty($email) || empty($password)) {
        $message = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        try {
            // Prepare the SQL query
            $stmt = $pdo->prepare("SELECT vendor_id, password FROM vendors WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Check if email exists
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $message = "Email not found.";
            } else {
                // Verify password
                $hashedPassword = $result['password'];
                if (!password_verify($password, $hashedPassword)) {
                    $message = "Incorrect password.";
                } else {
                    // Successful login
                    $_SESSION['vendor_id'] = $result['vendor_id']; // Store vendor_id in session
                    $_SESSION['email'] = $email; // Optionally store email in session
                    header('Location: index.php'); // Redirect to dashboard or home page
                    exit();
                }
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    // Store message and message type in session
    $_SESSION['message'] = $message;
    $_SESSION['msg_type'] = $msg_type;

    // Redirect to the login page
    header('Location: login.php');
    exit();
}
