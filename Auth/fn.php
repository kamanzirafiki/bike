<?php
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user input from form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Email and Password are required.";
        header("Location: login.php");
        exit();
    }

    try {
        // Prepare SQL query
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Start session and set session variables
            session_start();
            session_regenerate_id(true); // Regenerate session ID for security
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the user dashboard or home page
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle error
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header("Location: login.php");
        exit();
    }
}
?>
