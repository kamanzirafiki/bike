<?php
session_start();
include '../db_connection.php'; // Ensure you have this file for DB connection

// Check if vendor_id is set in session
if (!isset($_SESSION['vendor_id'])) {
    die("Vendor not logged in.");
}

// Fetch vehicles for the current vendor
$vendor_id = $_SESSION['vendor_id'];
$sql = "SELECT * FROM bikes_scooters WHERE vendor_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$vendor_id]);
$vehicles = $stmt->fetchAll();

// Fetch distinct types from the bikes_scooters table
$sql_types = "SELECT DISTINCT type FROM bikes_scooters";
$stmt_types = $pdo->prepare($sql_types);
$stmt_types->execute();
$types = $stmt_types->fetchAll(PDO::FETCH_COLUMN);

// Check if there's a status message to display
$status = isset($_GET['status']) ? $_GET['status'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View My Vehicles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
    body {
        font-family: "Arial", sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    main {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
        margin-left: 260px; /* Increased left margin to account for sidebar */
    }

    .table-container {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f8f9fa;
        color: #333;
    }

    .table-responsive {
        margin-bottom: 20px;
    }

    .no-vehicles {
        text-align: center;
        padding: 20px;
        font-size: 1em;
        color: #555;
    }

    .search-bar {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-bar .form-group {
        margin-bottom: 10px;
    }

    .vehicle-image {
        width: 100px;
        border-radius: 8px;
    }

    /* Custom Print Button Styling */
    .custom-print-btn {
        background-color: #007bff; /* Bootstrap primary blue */
        color: white;
        padding: 5px 10px;
        font-size: 0.875rem; /* Smaller font size */
        border: none;
        border-radius: 4px;
        height: 35px; /* Reduced height */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-print-btn:hover {
        background-color: #0069d9; /* Darker blue on hover */
    }

    /* Print-specific styles */
    @media print {
        body {
            background-color: white;
        }

        /* Hide elements not needed for printing */
        .search-bar, 
        .btn, 
        .modal, 
        .navbar, 
        .sidebar {
            display: none !important;
        }

        main {
            margin-left: 0;
            padding: 0;
        }

        table {
            border: 1px solid #000;
        }

        th, td {
            border: 1px solid #000;
        }
    }
    #printButton {
            background-color: #007bff;
            /* Blue background */
            color: #fff;
            /* White text */
            border: none;
            /* Remove border */
            border-radius: 10px;
            /* Rounded corners */
            padding: 10px 20px;
            /* Adjust padding */
            font-size: 16px;
            /* Font size */
            cursor: pointer;
            /* Pointer cursor */
            display: inline-flex;
            /* Align icon and text */
            align-items: center;
            /* Center content vertically */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            /* Optional shadow for 3D effect */
        }

        #printButton i {
            margin-right: 8px;
            /* Space between icon and text */
        }

        #printButton:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
        }
    </style>

</head>
<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Include sidebar -->
    <?php include 'sidebar.php'; ?>

    <main>
        <h2>My Vehicles</h2>

        <!-- Search bar -->
        <div class="search-bar">
            <div class="form-group d-flex align-items-center">
                <label for="entries" class="mr-2 mb-0">Show</label>
                <select id="entries" class="form-control mr-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="search" class="mr-2 mb-0">Search:</label>
                <input type="text" id="search" class="form-control mr-4" placeholder="Search vehicles...">
                <button class="custom-print-btn" id="printButton"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <!-- Display Vehicles -->
        <?php if (!empty($vehicles)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="vehiclesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Model</th>
                            <th>Plate Number</th>
                            <th>Details</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['name']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['plate_number']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['details']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                <td>
                                    <?php if (!empty($vehicle['image'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($vehicle['image']); ?>" class="vehicle-image" alt="Vehicle Image">
                                    <?php else: ?>
                                        <span>No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-primary btn-sm btn-custom edit-btn" data-toggle="modal" data-target="#editModal"
                                            data-id="<?php echo $vehicle['bike_scooter_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                            data-model="<?php echo htmlspecialchars($vehicle['model']); ?>"
                                            data-type="<?php echo htmlspecialchars($vehicle['type']); ?>"
                                            data-image="<?php echo htmlspecialchars($vehicle['image']); ?>"
                                            data-details="<?php echo htmlspecialchars($vehicle['details']); ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-vehicles">No vehicles found.</p>
        <?php endif; ?>
    </main>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_vehicle.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="editVehicleId">
                    
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editModel">Model</label>
                        <input type="text" class="form-control" id="editModel" name="model" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editType">Type</label>
                        <select class="form-control" id="editType" name="type" required>
                            
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editDetails">Details</label>
                        <textarea class="form-control" id="editDetails" name="details" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="editImage">Image</label>
                        <input type="file" class="form-control-file" id="editImage" name="image">
                        <small class="form-text text-muted">Current Image: <span id="currentImage"></span></small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                
            </form>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Search functionality
        $(document).ready(function () {
            $('#search').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#vehiclesTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Print functionality
            $('#printButton').on('click', function () {
                window.print();
            });

            // Populate the modal with vehicle data when the edit button is clicked
            $('.edit-btn').on('click', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const model = $(this).data('model');
                const type = $(this).data('type');
                const image = $(this).data('image');
                const details = $(this).data('details');

                $('#editVehicleId').val(id);
                $('#editName').val(name);
                $('#editModel').val(model);
                $('#editType').val(type);
                $('#editDetails').val(details);
                $('#currentImage').text(image ? image : 'No Image');
            });

            // Show the number of entries based on the select option
            $('#entries').on('change', function () {
                var entries = parseInt($(this).val());
                $('#vehiclesTable tbody tr').each(function (index) {
                    if (index < entries) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Trigger the change event on page load to set the initial number of entries
            $('#entries').trigger('change');
        });
    </script>
</body>
</html>
