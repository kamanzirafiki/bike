<?php
ob_start(); // Start output buffering

include('../includes/header.php'); 
include '../db_connection.php'; 

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ./Auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found");
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Check if form is submitted to update password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: updatepassword.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match.";
        header("Location: updatepassword.php");
        exit();
    }

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error_message'] = "Current password is incorrect.";
        header("Location: updatepassword.php");
        exit();
    }

    // Update password in the database
    try {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
        $stmt->bindParam(':password', $new_password_hashed);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Password updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating password.";
        }

        header("Location: updatepassword.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header("Location: updatepassword.php");
        exit();
    }
}

ob_end_flush(); // End output buffering and flush the buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: url('background.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
        }

        .sidebar {
            width: 250px;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-img {
            font-size: 100px;
            color: #333;
        }

        .username {
            margin-top: 10px;
            font-size: 18px;
            color: #333;
        }

        .nav-links ul {
            list-style: none;
            padding: 0;
        }

        .nav-links ul li {
            margin-bottom: 15px;
        }

        .nav-links ul li a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
        }

        .nav-links ul li.active a {
            color: #e74c3c;
            font-weight: bold;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .profile-form {
            max-width: 500px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn-save {
            padding: 10px 20px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-save:hover {
            background-color: #c0392b;
        }

        .alert {
            padding: 15px;
            margin: 20px auto; /* Center the alert and add vertical spacing */
            border-radius: 4px;
            position: relative;
            display: flex;
            align-items: center;
            max-width: 500px; /* Limit the width of the alert messages */
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert .icon {
            font-size: 20px;
            margin-right: 10px;
        }

        .alert-success .icon {
            color: #155724;
        }

        .alert-error .icon {
            color: #721c24;
        }

        .alert .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 16px;
            color: #333;
        }

        .alert .close-btn:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="container">
            <?php include('sidebar.php'); ?>
            <div class="main-content">
                <h2 class="section-title">UPDATE PASSWORD</h2>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle icon"></i>
                        <?php
                        echo htmlspecialchars($_SESSION['success_message']);
                        unset($_SESSION['success_message']);
                        ?>
                        <span class="close-btn">&times;</span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle icon"></i>
                        <?php
                        echo htmlspecialchars($_SESSION['error_message']);
                        unset($_SESSION['error_message']);
                        ?>
                        <span class="close-btn">&times;</span>
                    </div>
                <?php endif; ?>

                <form action="updatepassword.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-save">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script>
        document.querySelectorAll('.alert .close-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
    </script>
</body>
</html>
