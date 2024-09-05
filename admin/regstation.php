<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}
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
            font-family: "Arial", sans-serif;
        }
        .main-content {
            display: flex;
            flex-grow: 1;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 700px;
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
        .map-container {
            height: 400px;
            margin-top: 1.5rem;
            border: 2px solid #ced4da;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Alert message styling */
        .alert {
            display: flex;
            align-items: center; /* Center items vertically */
            padding: 10px 15px; /* Reduce padding */
            margin-bottom: 20px; /* Spacing between alerts and other elements */
            border-radius: 5px; /* Rounded corners */
            height: auto; /* Allow dynamic height based on content */
            width: 100%; /* Match width of input */
        }
        
        .alert .alert-close {
            cursor: pointer;
            margin-left: auto; /* Align close button to the right */
            font-size: 1.2rem; /* Adjust icon size */
        }

        .alert i {
            margin-right: 10px; /* Space between icon and text */
        }
    </style>
    <!-- Include Google Maps JavaScript API with Places library -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMyqbJRAcMqB2HNojuv7vycasGow8mHLU&callback=initMap&libraries=places" async defer></script>
    <script>
        let map, marker, geocoder, searchBox;

        function initMap() {
            const initialPosition = { lat: -1.957875, lng: 30.112735 }; // Default coordinates

            // Initialize the map
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: initialPosition,
            });

            // Initialize marker
            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                draggable: true,
            });

            geocoder = new google.maps.Geocoder();

            // Initialize the search box and link it to the UI element.
            const input = document.getElementById("location-search");
            const options = {
                fields: ["formatted_address", "geometry", "name"],
                strictBounds: false,
            };
            searchBox = new google.maps.places.SearchBox(input, options);

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // For each place, get the icon, name and location.
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }

                    // Move the marker to the selected location
                    marker.setPosition(place.geometry.location);

                    // Update the map's center to the selected place
                    map.setCenter(place.geometry.location);

                    // Update the address field
                    document.getElementById("address").value = place.formatted_address;
                });
            });

            // Add a listener for the marker drag end event
            google.maps.event.addListener(marker, 'dragend', function () {
                geocodePosition(marker.getPosition());
            });

            // Listen for map clicks to move the marker and get address
            google.maps.event.addListener(map, 'click', function (event) {
                marker.setPosition(event.latLng);
                geocodePosition(event.latLng);
            });
        }

        // Function to get the address for a given position
        function geocodePosition(position) {
            geocoder.geocode({ location: position }, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        document.getElementById('address').value = results[0].formatted_address;
                    }
                }
            });
        }
    </script>
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
                                <h3 class="mb-5">Register Station</h3>

                                <?php if ($message): ?>
                                    <div id="alertMessage" class="alert <?= strpos($message, 'alert-success') !== false ? 'alert-success' : (strpos($message, 'alert-warning') !== false ? 'alert-warning' : 'alert-danger') ?>">
                                        <i class="fas <?= strpos($message, 'alert-success') !== false ? 'fa-check-circle' : (strpos($message, 'alert-warning') !== false ? 'fa-exclamation-triangle' : 'fa-exclamation-circle') ?>"></i>
                                        <span><?= $message ?></span>
                                        <span class="alert-close" onclick="this.parentElement.style.display='none';">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Location search input -->
                                <div class="form-outline mb-4">
                                    <input type="text" id="location-search" class="form-control form-control-lg" placeholder="Search for a location..." />
                                    <label class="form-label" for="location-search">Location Search</label>
                                </div>

                                <!-- Station registration form -->
                                <form action="regstationfn.php" method="post">
                                    <div class="form-outline mb-4">
                                        <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                                        <label class="form-label" for="name">Station Name</label>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <input type="text" id="address" name="address" class="form-control form-control-lg" required />
                                        <label class="form-label" for="address">Address</label>
                                    </div>
                                    <!-- Map container to pick the address -->
                                    <div id="map" class="map-container"></div>

                                    <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">Register Station</button>
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
</body>
</html>
