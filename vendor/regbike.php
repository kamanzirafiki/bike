<?php
session_start();

// Debugging: Check if session variables are set
// Uncomment the following lines to debug
// var_dump($_SESSION['message']);
// var_dump($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Bike/Scooter</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            max-width: 800px; /* Adjusted width */
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-bottom: 1px solid #007bff;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004494;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .form-row .form-group {
            margin-bottom: 1rem;
        }
        .form-row .form-group input,
        .form-row .form-group textarea {
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-row .form-group input:focus,
        .form-row .form-group textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div id="sidebar-overlay" class="overlay"></div>
            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
                <?php include 'header.php'; ?>
                <div class="container mt-3">
                    <!-- Alert messages -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['message']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php 
                        // Clear the message after displaying
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    <?php endif; ?>
                    <!-- Registration form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Register Bike/Scooter</h4>
                        </div>
                        <div class="card-body">
                            <form action="regbikefn.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="vendor_id" value="<?php echo htmlspecialchars($_SESSION['vendor_id']); ?>">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="type">Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="Bike">Bike</option>
                                            <option value="Scooter">Scooter</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="model">Model</label>
                                        <input type="text" class="form-control" id="model" name="model" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="plate_number">Plate Number</label>
                                        <input type="text" class="form-control" id="plate_number" name="plate_number" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="details">Details/Description</label>
                                        <textarea class="form-control" id="details" name="details" rows="3"></textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control-file" id="image" name="image" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript for sidebar toggle and overlay
        $('#sidebar-toggle').on('click', function () {
            $('#sidebar').toggleClass('active');
            $('#sidebar-overlay').toggleClass('d-block');
        });

        $('#sidebar-overlay').on('click', function () {
            $('#sidebar').removeClass('active');
            $(this).removeClass('d-block');
        });
    </script>
</body>
</html>
