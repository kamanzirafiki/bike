<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Rental Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #000;
        }
        .navbar-brand {
            color: #fff;
        }
        .navbar-nav .nav-link {
            color: #fff;
            margin-right: 20px;
            padding: 10px 15px;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }
        .navbar-nav .nav-link:hover {
            background-color: #a4aba6;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .navbar-nav .nav-link.dropdown-toggle {
            padding: 10px 15px;
        }
        .navbar-nav .nav-link.dropdown-toggle:hover {
            background-color: transparent;
            color: #fff;
            box-shadow: none;
        }
        .hero-section {
            position: relative;
            background-image: url('images/bike2.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            color: white;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: absolute;
            top: 50%;
            left: 75%;
            transform: translate(-50%, -50%);
            text-align: left;
            z-index: 1;
        }
        .hero-content h3 {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero-content p {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .hero-content .btn {
            font-size: 16px;
            padding: 10px 20px;
            background-color: #a4aba6;
            border: none;
            border-radius: 50px;
            color: #fff;
        }
        .hero-content .btn:hover {
            background-color: #8f8989;
        }
        .form-control {
            width: 250px;
        }
        .dropdown-menu {
            background-color: #A4ABA6;
            color: #fff;
        }
        .dropdown-menu .dropdown-item {
            color: #fff;
        }
        .dropdown-menu .dropdown-item:hover {
            background-color: #8f8989;
        }
        .dropdown-menu .dropdown-item:focus {
            background-color: #A4ABA6; /* Ensure it stays the same color when focused */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Brand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about us.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./bike.php">Bike Listing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">FAQs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact us.php">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-3">
                    <li class="nav-item dropdown">
                        <?php
                        // Debug session variable
                        // echo '<pre>'; print_r($_SESSION); echo '</pre>';
                        
                        $userName = isset($_SESSION['username']) ? $_SESSION['username'] : '';
                        $firstName = strtok($userName, ' ');
                        ?>
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($firstName); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="<?php echo isset($_SESSION['username']) ? './userdash/profile.php' : './Auth/login.php'; ?>">Me</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo isset($_SESSION['username']) ? './userdash/my_booking.php' : './Auth/login.php'; ?>">My Booking</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../Auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <form class="d-flex ms-3" action="search.php" method="get">
                            <input class="form-control me-2" type="search" name="query" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-light" type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
