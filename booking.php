<?php
include 'db_connection.php'; // Ensure this file contains the PDO connection
session_start(); // Assuming you're using sessions to handle logged-in users

// Check if bike_scooter_id is provided
if (!isset($_GET['bike_scooter_id'])) {
    echo "Vehicle not specified.";
    exit;
}

$bike_scooter_id = $_GET['bike_scooter_id'];

// Assuming the user is logged in and their user_id is stored in the session
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

try {
    // Fetch the vehicle details from the database
    $sql = "SELECT * FROM bikes_scooters WHERE bike_scooter_id = :bike_scooter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $stmt->execute();
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        echo "Vehicle not found.";
        exit;
    }

    // Fetch distinct pickup and dropoff stations from the routes table
    $pickup_sql = "SELECT DISTINCT pickup_station FROM routes";
    $pickup_stmt = $pdo->query($pickup_sql);
    $pickup_stations = $pickup_stmt->fetchAll(PDO::FETCH_ASSOC);

    $dropoff_sql = "SELECT DISTINCT dropoff_station FROM routes";
    $dropoff_stmt = $pdo->query($dropoff_sql);
    $dropoff_stations = $dropoff_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #00000024, #00000024), url(../images/bike2.jpg) no-repeat center;
            background-size: cover;
            font-family: Arial, sans-serif;
            height: 100vh;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .booking-form {
            border-radius: 10px;
            padding: 45px;
            width: 33%;
            min-height: 500px;
            backdrop-filter: blur(8px);
            background-color: #ffffff85;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-select, .form-control {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            padding: 10px;
            width: 100%;
        }

        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .form-label {
            font-size: 14px;
            text-align: left;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="booking-form">
            <h2>Booking for <?php echo htmlspecialchars($vehicle['name']); ?></h2>
            <p><?php echo htmlspecialchars($vehicle['details'] ?? 'No details available'); ?></p>

            <form action="book.php" method="POST">
                <input type="hidden" name="bike_scooter_id" value="<?php echo htmlspecialchars($vehicle['bike_scooter_id']); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> <!-- Pass user_id -->

                <div class="mb-3">
                    <label for="pickup_station" class="form-label">Pickup Station</label>
                    <select name="pickup_station" id="pickup_station" class="form-select" required>
                        <option value="">Select Pickup Station</option>
                        <?php foreach ($pickup_stations as $station) : ?>
                            <option value="<?php echo htmlspecialchars($station['pickup_station']); ?>">
                                <?php echo htmlspecialchars($station['pickup_station']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dropoff_station" class="form-label">Dropoff Station</label>
                    <select name="dropoff_station" id="dropoff_station" class="form-select" required>
                        <option value="">Select Dropoff Station</option>
                        <?php foreach ($dropoff_stations as $station) : ?>
                            <option value="<?php echo htmlspecialchars($station['dropoff_station']); ?>">
                                <?php echo htmlspecialchars($station['dropoff_station']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="booking_date" class="form-label">Booking Date</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="total_price" class="form-label">Total Price</label>
                    <input type="number" name="total_price" id="total_price" class="form-control" value="<?php echo htmlspecialchars($vehicle['price'] ?? ''); ?>" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
