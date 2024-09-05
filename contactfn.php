<?php
// Include database connection file
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $message = $_POST['message'];

    // Prepare the SQL statement
    $sql = "INSERT INTO contact_us_queries (full_name, email, phone_number, message, submitted_at) 
            VALUES (:full_name, :email, :phone_number, :message, :submitted_at)";

    // Prepare and execute the statement using PDO
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':submitted_at', $submitted_at);

    // Set the current timestamp for submitted_at
    $submitted_at = date('Y-m-d H:i:s');

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the form page with a success message
        header("Location: contact us.php?status=success");
        exit();
    } else {
        // Redirect back to the form page with an error message
        header("Location: contact us..php?status=error");
        exit();
    }
}
?>
