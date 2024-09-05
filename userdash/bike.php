<?php
include '../db_connection.php'; 
include 'header.php';

try {
    
    $sql = "SELECT * FROM bikes_scooters WHERE available = 'returned'";
    $stmt = $pdo->query($sql);


    if ($stmt) {
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $vehicles = []; 
        echo "Failed to fetch vehicles.";
    }
} catch (PDOException $e) {
    
    echo "Error: " . $e->getMessage();
    $vehicles = []; 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returned Bikes & Scooters</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            background-color: black;
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

        /* Add your existing modal styles here */
        #feedback-section {
            position: relative; /* Ensure it's positioned relative */
            z-index: 10; /* Higher value to bring it above other elements */
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6); /* Slightly darker background for the modal overlay */
            backdrop-filter: blur(5px); /* Optional: Add a blur effect to the background */
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 40%; /* Reduced width */
            max-width: 500px; /* Optional: Set a maximum width */
            animation: shake 0.5s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: Add a shadow for a cool effect */
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            50% {
                transform: translateX(10px);
            }
            75% {
                transform: translateX(-10px);
            }
            100% {
                transform: translateX(0);
            }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .modal-buttons button {
            background-color: #4CAF50; /* Green background */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .modal-buttons button.cancel {
            background-color: #f44336; /* Red background for cancel */
        }

        .modal-buttons button:hover {
            background-color: #45a049; /* Darker green */
        }

        .modal-buttons button.cancel:hover {
            background-color: #c62828; /* Darker red for cancel */
        }
    </style>
</head>

<body>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php if (!empty($vehicles)) : ?>
                <?php foreach ($vehicles as $vehicle) : ?>
                    <div class="swiper-slide">
                        <img src="../company/uploads/<?php echo htmlspecialchars($vehicle['image'] ?? 'default_image.jpg'); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
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
                <p class="text-center">No returned vehicles found.</p>
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
    
    <?php include '../includes/footer.php'; ?>
</body>

</html>
