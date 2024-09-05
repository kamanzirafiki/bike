<?php
include '../db_connection.php';
include 'header.php';

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['bike_scooter_id'])) {
    echo "Vehicle not specified.";
    exit;
}

$bike_scooter_id = $_GET['bike_scooter_id'];

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Retrieve the vehicle details using prepared statements
    $sql = "SELECT * FROM bikes_scooters WHERE bike_scooter_id = :bike_scooter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $stmt->execute();
    $vehicle = $stmt->fetch();

    // Check if the vehicle exists
    if (!$vehicle) {
        echo "Vehicle not found.";
        exit;
    }

    // Determine price per kilometer based on vehicle type
    $price_per_km = ($vehicle['type'] === 'scooter') ? 100 : 50; // 100 Rwf for scooters, 50 Rwf for bikes

    // Retrieve stations for both pickup and dropoff in one query
    $stations_sql = "SELECT station_id, name, address FROM stations";
    $stations_stmt = $pdo->query($stations_sql);
    $stations = $stations_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$stations) {
        echo "No stations available.";
        exit;
    }

} catch (PDOException $e) {
    // Handle any potential errors
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form with Map Autofill</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMyqbJRAcMqB2HNojuv7vycasGow8mHLU&libraries=places"></script>
    <style>
        /* Add your CSS styles here */
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Arial", sans-serif;
        }

        .container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            width: 100%;
            margin: 0 auto;
        }

        .main-wrapper {
            width: 100%;
            position: relative;
            display: flex;
            flex-direction: row;
            min-height: 90vh;
        }

        .form-input,
        .form-textarea,
        .form-select {
            padding: 20px 25px 15px;
            width: 100%;
            border-radius: 5px;
            outline: none;
            font-size: 20px;
            line-height: 30px;
            font-weight: 400;
            box-sizing: border-box;
            border: 1px solid gray;
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.5;
        }

        .btn-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px 0 0;
        }

        .btn-wrap button {
            padding: 0 32px;
            font-size: 18px;
            line-height: 48px;
            border: 1px solid transparent;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.5s ease;
            background-color: black;
            cursor: pointer;
            color: white;
        }

        .myMapContainer {
            width: 80%;
            height: 90vh;
        }

        .map-container {
            width: 100%;
            height: 100%;
        }

        .myRoutePrice {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="main-wrapper d-flex flex-row">

            <div class="booking_container shadow p-3">
                <h2 class="form-head mt-3">Book Your Ride</h2>

                <form action="book.php" method="post" class="form-wrapper pb-5" id="bookingForm">
                    <input type="hidden" name="bike_scooter_id" value="<?php echo htmlspecialchars($bike_scooter_id); ?>">
                    <input type="hidden" name="total_price" id="total_price">
                    <input type="hidden" name="price_per_km" value="<?php echo htmlspecialchars($price_per_km); ?>"> <!-- Pass price per km -->

                    <!-- Pickup Station Selection -->
                    <div class="mb-3">
                        <select name="pickup_station" id="pickup_station" class="form-select" required>
                            <option value="">Select Pickup Station</option>
                            <?php foreach ($stations as $station) : ?>
                                <option value="<?php echo htmlspecialchars($station['station_id']); ?>" data-address="<?php echo htmlspecialchars($station['address']); ?>">
                                    <?php echo htmlspecialchars($station['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dropoff Station Selection -->
                    <div class="mb-3">
                        <select name="dropoff_station" id="dropoff_station" class="form-select" required>
                            <option value="">Select Dropoff Station</option>
                            <?php foreach ($stations as $station) : ?>
                                <option value="<?php echo htmlspecialchars($station['station_id']); ?>" data-address="<?php echo htmlspecialchars($station['address']); ?>">
                                    <?php echo htmlspecialchars($station['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <div class="myRoutePrice" id="myRoutePrice"></div>
                    </div>

                    <div class="btn-wrap">
                        <button type="submit">Book</button>
                    </div>
                </form>
            </div>

            <div class="myMapContainer">
                <div id="map" class="map-container"></div>
            </div>
        </div>
    </div>

    <script>
        let map, directionsService, directionsRenderer;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: -1.970579, lng: 30.104429 },
                zoom: 13,
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            document.getElementById('pickup_station').addEventListener('change', calculateRoute);
            document.getElementById('dropoff_station').addEventListener('change', calculateRoute);
        }

        function calculateRoute() {
            const pickupSelect = document.getElementById('pickup_station');
            const dropoffSelect = document.getElementById('dropoff_station');

            // Ensure both pickup and dropoff are selected
            if (pickupSelect.value === "" || dropoffSelect.value === "") {
                document.getElementById('myRoutePrice').innerHTML = "";
                directionsRenderer.set('directions', null);
                return;
            }

            const pickupStation = pickupSelect.selectedOptions[0].getAttribute('data-address');
            const dropoffStation = dropoffSelect.selectedOptions[0].getAttribute('data-address');

            if (pickupStation && dropoffStation) {
                const request = {
                    origin: pickupStation,
                    destination: dropoffStation,
                    travelMode: google.maps.TravelMode.DRIVING,
                };

                directionsService.route(request, function (result, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(result);

                        const route = result.routes[0].legs[0];
                        const distance = route.distance.value / 1000; // Distance in kilometers
                        const distanceText = route.distance.text;
                        const durationText = route.duration.text;

                        const pricePerKm = parseFloat(document.querySelector('input[name="price_per_km"]').value);
                        const totalPrice = distance * pricePerKm;

                        // Format the total price to include commas and two decimal places
                        const formattedTotalPrice = totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                        // Update the total price in the form
                        document.getElementById('total_price').value = totalPrice.toFixed(2);

                        // Populate the route details
                        document.getElementById('myRoutePrice').innerHTML = `
                            <div class="route-item">
                                <strong>Distance:</strong> ${distanceText} <br>
                                <strong>Duration:</strong> ${durationText} <br>
                                <strong>Price / KM:</strong> Rwf ${pricePerKm.toFixed(2)} <br>
                                <strong>Total Price:</strong> Rwf ${formattedTotalPrice}
                            </div>
                        `;
                    } else {
                        console.log('Directions request failed due to ' + status);
                    }
                });
            }
        }

        window.onload = initMap;
    </script>
</body>

</html>
