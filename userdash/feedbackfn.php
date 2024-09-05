<?php
// Include your database connection
include '../db_connection.php'; // Update this path to your actual connection file

session_start(); // Start session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Show an alert message and then redirect to the login page
    echo "<script>
            alert('You must be logged in to submit feedback. Please log in first.');
            window.location.href = '../Auth/login.php';
          </script>";
    exit; // Stop script execution after the redirect
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $feedback_text = trim($_POST['feedbackText']); // Get feedback text from form
    $rating = intval($_POST['rate']); // Get rating from form
    $base_url = "http://localhost";

    // Get the relative redirect URL from POST data, and ensure it is properly sanitized
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : '/'; 

    // Build the full URL by concatenating the base URL with the redirect URL
    $full_redirect_url = $base_url . htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8');



    // Validation: Check if feedback text and rating are provided
    if (empty($feedback_text) || $rating == 0) {
        echo "<script>alert('Please provide a comment and select a rating.'); window.history.back();</script>";
        exit;
    }

    try {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, feedback_text, rating, created_at) VALUES (:user_id, :feedback_text, :rating, NOW())");
        
        // Bind parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':feedback_text', $feedback_text, PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>
            alert('Feedback submitted successfully.');
            window.location.href = '" . $full_redirect_url . "';
          </script>";
    exit;
    
        
        } else {
            echo "<script>alert('Error: Unable to submit feedback.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
