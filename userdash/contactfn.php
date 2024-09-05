<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Autoload PHPMailer
include '../db_connection.php';   // Include database connection

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
        // Now that the data is saved, send an email
        $mail = new PHPMailer(true);  // Create a new PHPMailer instance

        try {
            //Server settings for localhost environment (Windows)
            $mail->isSMTP();                                         // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
            $mail->Username   = 'bikescooters056@gmail.com';          // SMTP username (sender email)
            $mail->Password   = 'itng fznu ocru uapj';                // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption
            $mail->Port       = 587;                                  // TCP port to connect to

            // Recipients
            $mail->setFrom('bikescooters056@gmail.com', 'Bike Scooter Rental');   // Sender email
            $mail->addAddress($email, $full_name);                              // Add recipient with dynamic name

            // Content
            $mail->isHTML(true);                                                // Set email format to HTML
            $mail->Subject = 'Thank You for Registering on Our Platform';

            // Email body with the personalized message
            $mail->Body    = "
                <p>Dear $full_name,</p>
                <p>Thank you for registering on our platform. We're excited to have you on board.</p>
                <p>Best regards,<br>Bike & Scooter Rental Team</p>
            ";

            // Send the email
            $mail->send();

            // After sending, show the success message using JavaScript
            echo "<script>
                    alert('Your message has been sent successfully. Please check your email for more details.');
                    window.location.href = 'contact us.php';
                  </script>";
        } catch (Exception $e) {
            // Email failed to send, but still show a success message for form submission
            echo "<script>
                    alert('Message sent, but email could not be delivered. Please try again later.');
                    window.location.href = 'contact us.php';
                  </script>";
        }
    } else {
        // Redirect back to the form page with an error message
        echo "<script>
                alert('There was an error submitting your message. Please try again.');
                window.location.href = 'contact us.php';
              </script>";
    }
}
?>
