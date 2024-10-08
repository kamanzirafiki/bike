<?php
include 'db_connection.php'; 
include './userdash/header.php';

try {
    // Fetch vehicles from the database
    $sql = "SELECT * FROM bikes_scooters";
    $stmt = $pdo->query($sql);

    // Check if the query executed successfully and fetch results
    if ($stmt) {
        $vehicles = $stmt->fetchAll();
    } else {
        $vehicles = []; // Set to an empty array if query fails
        echo "Failed to fetch vehicles.";
    }
} catch (PDOException $e) {
    // Handle any errors during the database operations
    echo "Error: " . $e->getMessage();
    $vehicles = []; // Set to an empty array in case of error
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Bikes & Scooters</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: Arial, sans-serif;
        }

        .swiper-container {
            width: 100%;
            height: 100vh;
            position: relative;
        }

        .swiper-slide {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: relative;
            padding-right: 5%;
        }

        .swiper-slide img {
            width: auto;
            max-height: 70vh;
            object-fit: contain;
        }

        .swiper-caption {
            position: absolute;
            left: 10%;
            top: 30%;
            text-align: left;
            max-width: 30%;
            color: black;
        }

        .swiper-caption h5 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .swiper-caption p {
            font-size: 1.2rem;
            margin: 5px 0;
        }

        .book-btn {
            background-color: #333;
            color: white;
            padding: 5px 15px;
            font-size: 1rem;
            text-transform: uppercase;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .book-btn:hover {
            background-color: #555;
        }

        .swiper-pagination-bullet {
            background: #333;
        }
        .dropdown-menu {
            background-color: black ;
            color: #fff;
        }
        .dropdown-menu .dropdown-item {
            color: #fff;
        }
        .dropdown-menu .dropdown-item:hover {
            background-color: #A4ABA6;
        }
        .dropdown-menu .dropdown-item:focus {
            background-color: #A4ABA6; /* Ensure it stays the same color when focused */
        }
    </style>
</head>

<body>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php if (!empty($vehicles)) : ?>
                <?php foreach ($vehicles as $vehicle) : ?>
                    <div class="swiper-slide">
                        <img src="company/uploads/<?php echo htmlspecialchars($vehicle['image'] ?? 'default_image.jpg'); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
                        <div class="swiper-caption">
                            <h5><?php echo htmlspecialchars($vehicle['name']); ?></h5>
                            <p><?php echo htmlspecialchars($vehicle['details'] ?? 'No details available'); ?></p>
                            <a href="booking.php?bike_scooter_id=<?php echo htmlspecialchars($vehicle['bike_scooter_id']); ?>" class="btn book-btn">
                                Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-center">No vehicles found.</p>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.swiper-container', {
                loop: true,
                autoplay: {
                    delay: 3000, // 3 seconds delay between slides
                    disableOnInteraction: false, // Keeps autoplay running after user interaction
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>

</html>
