<?php
include '../db_connection.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user input from form
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Initialize message variable
    $message = "";
    $msg_type = "danger"; // Default message type

    // Validate inputs
    if (empty($fullName) || empty($email) || empty($phoneNumber) || empty($password) || empty($confirmPassword)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (strlen($phoneNumber) < 10) {
        $message = "Phone number must be at least 10 digits long.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $emailExists = $stmt->fetchColumn();

            if ($emailExists) {
                $message = "Email is already registered.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Prepare the SQL query
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, created_at, updated_at) VALUES (:username, :password, :email, :phone, NOW(), NOW())");
                $stmt->execute([
                    ':username' => $fullName,
                    ':password' => $hashedPassword,
                    ':email' => $email,
                    ':phone' => $phoneNumber,
                ]);

                $message = "Registration successful!";
                $msg_type = "success"; // Set message type to success

                // Set a popup message in the session
                $_SESSION['popup_message'] = $message;
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    // Store message and message type in session
    $_SESSION['message'] = $message;
    $_SESSION['msg_type'] = $msg_type;

    // Redirect to the registration page
    header('Location: index.php');
    exit();
}
