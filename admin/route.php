<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

require '../db_connection.php';

// Fetch stations from the database
$query = "SELECT name FROM stations";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
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
            border: 1px solid #ced4da; 
            background-color: #fff; 
            color: #000; 
            transition: none; 
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?php include 'sidebar.php'; ?>

            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
                <!-- Include header -->
                <?php include 'header.php'; ?>

                <!-- Main content -->
                <div class="main-content">
                    <div class="container">
                        <div class="card shadow-2-strong">
                            <div class="card-body">
                                <h3 class="mb-5">Add Route</h3>

                                <?php if ($message): ?>
                                    <div id="alertMessage" class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : (strpos($message, 'Route already exists') !== false ? 'alert-warning' : 'alert-success') ?>">
                                        <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="alert-close" onclick="this.parentElement.style.display='none';">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <form id="addRouteForm" action="routefn.php" method="post">
                                    <div class="form-outline mb-4">
                                        <input list="pickup_stations" id="pickup_station" name="pickup_station" class="form-control form-control-lg" required placeholder="Select or enter Pickup Station">
                                        <datalist id="pickup_stations">
                                            <?php foreach ($stations as $station): ?>
                                                <option value="<?= htmlspecialchars($station['name'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input list="dropoff_stations" id="dropoff_station" name="dropoff_station" class="form-control form-control-lg" required placeholder="Select or enter Dropoff Location">
                                        <datalist id="dropoff_stations">
                                            <?php foreach ($stations as $station): ?>
                                                <option value="<?= htmlspecialchars($station['name'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Add Route</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
