<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Station</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            display: flex;
            flex-grow: 1;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 500px;
            margin-top: 5%;
        }
        .card {
            border-radius: 1rem;
            background-color: rgba(255, 255, 255, 0.5);
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 4rem;
            text-align: center;
        }
        .form-control {
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #508bfc;
            border-color: #508bfc;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #4178d3;
            border-color: #4178d3;
            transform: translateY(-2px);
        }
        .alert {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.9rem;
            line-height: 1.2;
            position: relative;
        }
        .alert i {
            margin-right: 0.3rem;
        }
        .alert-close {
            margin-left: 1rem;
            cursor: pointer;
        }
        .sidebar {
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform 0.4s ease-out;
            background-color: #343a40;
        }

        .sidebar .list-group-item {
            border-radius: 0;
            border: 1px solid #495057;
            background-color: #343a40;
            color: #f8f9fa;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }

        .sidebar .list-group-item:hover {
            background-color: #495057 !important;
            border-color: #6c757d;
        }

        .sidebar .list-group-item.active {
            background-color: #007bff !important;
            border-color: #007bff;
            color: white;
        }

        .overlay {
            display: none;
            background-color: rgb(0 0 0 / 45%);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99;
        }

        .overlay.d-block {
            display: block;
        }

        @import url('https://fonts.googleapis.com/css2?family=Barlow&display=swap');

        body {
            font-family: 'Barlow', sans-serif;
        }

        a:hover {
            text-decoration: none;
        }

        .border-left {
            border-left: 2px solid var(--primary) !important;
        }

        .navbar-nav .nav-item .nav-link {
            color: #333;
        }

        .navbar-nav .nav-item .nav-link:hover {
            color: #007bff;
        }

        .dropdown-menu {
            right: 0;
            left: auto;
        }

        @media screen and (max-width: 767px) {
            .sidebar {
                max-width: 18rem;
                transform: translateX(-100%);
                transition: transform 0.4s ease-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar (copy the same code or include the same PHP file used in index.php) -->
            <?php include 'sidebar.php'; ?>

            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
                <!-- Include header (same as in index.php) -->
                <?php include 'header.php'; ?>

                <!-- Main content -->
                <div class="main-content">
                    <div class="container">
                        <div class="card shadow-2-strong">
                            <div class="card-body">
                                <h3 class="mb-5">Register Station</h3>

                                <?php if ($message): ?>
                                    <div id="alertMessage" class="alert <?= strpos($message, 'alert-success') !== false ? 'alert-success' : (strpos($message, 'alert-warning') !== false ? 'alert-warning' : 'alert-danger') ?>">
                                        <span><?= $message ?></span>
                                        <span class="alert-close" onclick="this.parentElement.style.display='none';">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <form action="regstationfn.php" method="post">
                                    <div class="form-outline mb-4">
                                        <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                                        <label class="form-label" for="name">Station Name</label>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <input type="text" id="address" name="address" class="form-control form-control-lg" />
                                        <label class="form-label" for="address">Address</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Register Station</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
