<?php
// Start output buffering
ob_start();

include('../includes/header.php'); 
include '../db_connection.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Auth/login.php');
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

// Check if form is submitted to update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate inputs
    if (empty($fullname) || empty($email) || empty($phone)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: profile.php");
        exit();
    }

    try {
        // Update user information in the database
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, phone = :phone WHERE user_id = :user_id");
        $stmt->bindParam(':username', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating profile.";
        }

        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header("Location: profile.php");
        exit();
    }
}

// End output buffering and flush the output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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

        .reg-date {
            margin-bottom: 20px;
            color: #888;
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
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="container">
            <?php include('sidebar.php'); ?>
            <div class="main-content">
                <h2 class="section-title">GENERAL SETTINGS</h2>
                <p class="reg-date">Reg Date - <?php echo htmlspecialchars($user['created_at']); ?></p>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo htmlspecialchars($_SESSION['success_message']);
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        <?php
                        echo htmlspecialchars($_SESSION['error_message']);
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="profile.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
