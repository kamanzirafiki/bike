<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Autoload PHPMailer, adjust path if necessary
include '../db_connection.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user input from form
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

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

                // Insert user into database
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, created_at, updated_at) VALUES (:username, :password, :email, :phone, NOW(), NOW())");
                $stmt->execute([
                    ':username' => $fullName,
                    ':password' => $hashedPassword,
                    ':email' => $email,
                    ':phone' => $phoneNumber,
                ]);

                // Send registration confirmation email using PHPMailer
                $mail = new PHPMailer(true); // Create instance of PHPMailer
                try {
                    //Server settings
                    $mail->isSMTP();                                      // Send using SMTP
                    $mail->Host       = 'smtp.gmail.com';                 // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                             // Enable SMTP authentication
                    $mail->Username   = 'bikescooters056@gmail.com';      // SMTP username (sender's email)
                    $mail->Password   = 'pknc lwut touu fkho';             // SMTP password (sender's email password)
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
                    $mail->Port       = 587;                              // TCP port to connect to

                    //Recipients
                    $mail->setFrom('bikescooters056@gmail.com', 'Bike Scooter Rental');  // Sender email
                    $mail->addAddress($email, $fullName);  // Recipient email (user who registered)

                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Registration Successful';
                    $mail->Body    = "Dear $fullName,<br><br>Thank you for registering on our platform.<br>We're excited to have you on board.<br><br>Best regards,<br>Bike & Scooter Rental Team";

                    $mail->send();
                    $message = "Registration successful! Confirmation email sent.";
                    $msg_type = "success";
                } catch (Exception $e) {
                    $message = "Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

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
