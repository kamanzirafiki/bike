<?php
session_start(); // Start the session

// Check if the admin is logged in
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
    <title>View Stations</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
            margin-left: 20%;
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

        .action-btns .btn {
            font-size: 0.8em;
            padding: 5px 10px;
        }

        .no-stations {
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
        }
        #printButton {
            background-color: #007bff; /* Blue background */
            color: #fff; /* White text */
            border: none; /* Remove border */
            border-radius: 10px; /* Rounded corners */
            padding: 10px 20px; /* Adjust padding */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor */
            display: inline-flex; /* Align icon and text */
            align-items: center; /* Center content vertically */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Optional shadow for 3D effect */
        }

        #printButton i {
            margin-right: 8px; /* Space between icon and text */
        }

        #printButton:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Include sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main content area -->
    <main>
        <h2>View Stations</h2>

        <div class="search-bar">
            <div class="form-group">
                <label for="entries">Show</label>
                <select id="entries" class="form-control">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                entries
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="search" class="mr-2 mb-0">Search:</label>
                <input type="text" id="search" class="form-control mr-4" placeholder="Search vehicles...">
                <button class="custom-print-btn" id="printButton"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <?php
        // Database connection
        include '../db_connection.php';

        // Prepare and execute the query using PDO
        $sql = "SELECT station_id, name, address, created_at FROM stations";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($stations) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover" id="stationsTable">';
            echo '<thead><tr><th>#</th><th>Name</th><th>Address</th><th>Created At</th><th>Actions</th></tr></thead>';
            echo '<tbody>';

            $counter = 1;
            foreach ($stations as $station) {
                echo '<tr>';
                echo '<td>' . $counter . '</td>';
                echo '<td>' . htmlspecialchars($station['name']) . '</td>';
                echo '<td>' . htmlspecialchars($station['address']) . '</td>';
                echo '<td>' . htmlspecialchars($station['created_at']) . '</td>';
                echo '<td class="action-btns">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editStationModal" 
                                data-id="' . htmlspecialchars($station['station_id']) . '"
                                data-name="' . htmlspecialchars($station['name']) . '"
                                data-address="' . htmlspecialchars($station['address']) . '">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteStationModal" 
                                data-id="' . htmlspecialchars($station['station_id']) . '">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                      </td>';
                echo '</tr>';
                $counter++;
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p class="no-stations">No stations available.</p>';
        }
        ?>

        <!-- Pagination -->
        <div class="d-flex justify-content-between">
            <div>Showing 1 to 10 of X entries (filtered from Y total entries)</div>
            <nav>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </main>

    <!-- Edit Station Modal -->
    <div class="modal fade" id="editStationModal" tabindex="-1" role="dialog" aria-labelledby="editStationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editStationForm" action="updatestation.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStationModalLabel">Edit Station</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="stationId" id="stationId">
                        <div class="form-group">
                            <label for="stationName">Station Name</label>
                            <input type="text" class="form-control" name="stationName" id="stationName" required>
                        </div>
                        <div class="form-group">
                            <label for="stationAddress">Station Address</label>
                            <input type="text" class="form-control" name="stationAddress" id="stationAddress" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Station Modal -->
    <div class="modal fade" id="deleteStationModal" tabindex="-1" role="dialog" aria-labelledby="deleteStationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStationModalLabel">Delete Station</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this station?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteStationForm" action="delete_station.php" method="POST">
                        <input type="hidden" name="stationId" id="deleteStationId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Populate edit modal with station data
        $('#editStationModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var stationId = button.data('id');
            var stationName = button.data('name');
            var stationAddress = button.data('address');

            // Set the values in the modal's form inputs
            var modal = $(this);
            modal.find('#stationId').val(stationId);
            modal.find('#stationName').val(stationName);
            modal.find('#stationAddress').val(stationAddress);
        });

        // Populate delete modal with station ID
        $('#deleteStationModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var stationId = button.data('id');

            // Set the value in the modal's hidden input
            var modal = $(this);
            modal.find('#deleteStationId').val(stationId);
        });

        // Search functionality for filtering the table
        $(document).ready(function() {
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#stationsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
</body>

</html>
