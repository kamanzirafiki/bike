<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./Auth/login.php");
    exit();
}

include '../db_connection.php';

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 10px;
        }
        .dashboard-nav {
            background-color: #343a40;
            color: #fff;
            border-radius: 10px;
        }
        .dashboard-nav .nav-link {
            color: #fff;
            padding: 15px;
            transition: background-color 0.3s;
        }
        .dashboard-nav .nav-link.active,
        .dashboard-nav .nav-link:hover {
            background-color: #495057;
        }
        .dashboard-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        .profile-header {
            background-color: #343a40;
            color: #fff;
            padding: 30px;
            border-radius: 10px 10px 0 0;
        }
        .profile-header img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-header h3 {
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: bold;
        }
        .profile-header p {
            margin-bottom: 0;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card dashboard-nav">
                <div class="profile-header text-center">
                    <img src="https://via.placeholder.com/120" alt="User Avatar">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="myinfo.php">My Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card dashboard-content">
                <h4>My Info</h4>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <a href="update_info.php" class="btn btn-primary">Update Info</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
