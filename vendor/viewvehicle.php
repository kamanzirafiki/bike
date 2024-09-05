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
            font-family: 'Barlow', sans-serif;
        }
        .vehicle-image {
            max-width: 100px;
            height: auto;
        }
        .btn-custom {
            height: 32px; /* Adjust the height as needed */
            padding: 4px 12px; /* Adjust padding to control button size */
            font-size: 14px; /* Adjust font size as needed */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-custom i {
            margin-right: 5px; /* Space between icon and text */
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
                <h1>My Vehicles</h1>

                <!-- Display the alert message based on status -->
                <?php if ($status === 'success'): ?>
                    <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                        Vehicle details updated successfully!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Model</th>
                        <th>Plate Number</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Details</th> <!-- Added Details column -->
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($vehicles)): ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['name']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['plate_number']); ?></td>
                                <td>
                                    <?php if (!empty($vehicle['image'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($vehicle['image']); ?>" class="vehicle-image" alt="Vehicle Image">
                                    <?php else: ?>
                                        <span>No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // Display status
                                    switch ($vehicle['approval_status']) {
                                        case 'approved':
                                            echo '<span class="badge badge-success">Approved</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="badge badge-danger">Rejected</span>';
                                            break;
                                        default:
                                            echo '<span class="badge badge-warning">Pending</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($vehicle['details']); ?></td> <!-- Display details -->
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-primary btn-sm btn-custom edit-btn" data-toggle="modal" data-target="#editModal"
                                            data-id="<?php echo $vehicle['bike_scooter_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                            data-model="<?php echo htmlspecialchars($vehicle['model']); ?>"
                                            data-image="<?php echo htmlspecialchars($vehicle['image']); ?>"
                                            data-details="<?php echo htmlspecialchars($vehicle['details']); ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No vehicles found</td> <!-- Adjusted colspan -->
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
                        <label for="editDetails">Details</label> <!-- Added Details field -->
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Populate the modal with vehicle data when the edit button is clicked
    $('.edit-btn').on('click', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const model = $(this).data('model');
        const image = $(this).data('image');
        const details = $(this).data('details');

        $('#editVehicleId').val(id);
        $('#editName').val(name);
        $('#editModel').val(model);
        $('#editDetails').val(details); // Populate details in the modal
        $('#currentImage').text(image ? image : 'No Image');
    });

    $('#sidebar-toggle').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#sidebar-overlay').toggleClass('d-block');
    });

    $('#sidebar-overlay').on('click', function () {
        $('#sidebar').removeClass('active');
        $(this).removeClass('d-block');
    });

    // Hide the alert after 5 seconds
    $(document).ready(function() {
        const alert = $('#successAlert');
        if (alert.length) {
            setTimeout(() => {
                alert.alert('close');
            }, 5000); // 5000 milliseconds = 5 seconds
        }
    });
</script>
</body>
</html>
