<?php
// Include database connection file
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if email already exists in the database
            $stmt = $pdo->prepare("SELECT id FROM subscribers WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<script>alert('This email is already subscribed.'); window.location.href='index.php';</script>";
            } else {
                // Insert new subscriber
                $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
                $stmt->bindParam(':email', $email);

                if ($stmt->execute()) {
                    // Send confirmation email using PHPMailer
                    $mail = new PHPMailer(true); // Create instance of PHPMailer
                    try {
                        //Server settings
                        $mail->isSMTP();                                      // Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';                 // Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                             // Enable SMTP authentication
                        $mail->Username   = 'bikescooters056@gmail.com';      // Your Gmail address (Sender)
                        $mail->Password   = 'npxc ntjj dtja iqkt';              // Use your App Password (if 2FA) or Gmail password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
                        $mail->Port       = 587;                              // TCP port to connect to Gmail

                        //Recipients
                        $mail->setFrom('bikescooters056@gmail.com', 'Bike Scooter Rental'); // Sender's email and name
                        $mail->addAddress($email);                               // Add recipient's email

                        // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = "Subscription Confirmation";
                        $mail->Body    = "Thank you for subscribing to our newsletter!";

                        $mail->send();
                        echo "<script>alert('Thank you for subscribing! A confirmation email has been sent to your address.'); window.location.href='index.php';</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('Subscription successful, but confirmation email failed to send. Mailer Error: {$mail->ErrorInfo}'); window.location.href='index.php';</script>";
                    }
                } else {
                    echo "<script>alert('Subscription failed. Please try again later.'); window.location.href='index.php';</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email address. Please try again.'); window.location.href='index.php';</script>";
    }
}
?>
