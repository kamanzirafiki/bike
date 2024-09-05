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
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9em;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        .no-stations {
            text-align: center;
            padding: 20px;
            font-size: 1em;
            color: #555;
        }

        .action-btns {
            text-align: center;
        }

        .action-btns .btn {
            font-size: 0.8em;
            padding: 5px 10px;
            margin: 0 5px;
            display: inline-flex;
            align-items: center;
        }

        .modal .form-group label {
            font-weight: bold;
        }

        .modal .form-control {
            width: 100%;
        }

        .modal-footer .btn {
            padding: 8px 20px;
        }

        .sidebar {
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform 0.4s ease-out;
            background-color: #343a40;
        }

        .sidebar .list-group-item {
            border-radius: 0;
            border: 1px solid #495057;
            background-color: #343a40;
            color: #f8f9fa;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }

        .sidebar .list-group-item:hover {
            background-color: #495057 !important;
            border-color: #6c757d;
        }

        .sidebar .list-group-item.active {
            background-color: #007bff !important;
            border-color: #007bff;
            color: white;
        }

        .overlay {
            display: none;
            background-color: rgb(0 0 0 / 45%);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99;
        }

        .overlay.d-block {
            display: block;
        }

        @import url('https://fonts.googleapis.com/css2?family=Barlow&display=swap');

        body {
            font-family: 'Barlow', sans-serif;
        }

        a:hover {
            text-decoration: none;
        }

        .border-left {
            border-left: 2px solid var(--primary) !important;
        }

        .navbar-nav .nav-item .nav-link {
            color: #333;
        }

        .navbar-nav .nav-item .nav-link:hover {
            color: #007bff;
        }

        .dropdown-menu {
            right: 0;
            left: auto;
        }

        @media screen and (max-width: 767px) {
            .sidebar {
                max-width: 18rem;
                transform: translateX(-100%);
                transition: transform 0.4s ease-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }
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
        <?php
        // Database connection
        include '../db_connection.php';

        // Prepare and execute the query using PDO
        $sql = "SELECT station_id, name, address, created_at FROM stations";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($stations) > 0) {
            echo '<table>';
            echo '<tr><th>Name</th><th>Address</th><th>Created At</th><th>Actions</th></tr>';

            foreach ($stations as $station) {
                echo '<tr>';
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
            }

            echo '</table>';
        } else {
            echo '<p class="no-stations">No stations available.</p>';
        }
        ?>
    </main>

    <!-- Modal for editing station -->
    <div class="modal fade" id="editStationModal" tabindex="-1" role="dialog" aria-labelledby="editStationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStationModalLabel">Edit Station</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editStationForm" method="post" action="updatestation.php">
                    <div class="modal-body">
                        <input type="hidden" name="station_id" id="stationId">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="stationName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="stationAddress" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Station</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for deleting station -->
    <div class="modal fade" id="deleteStationModal" tabindex="-1" role="dialog" aria-labelledby="deleteStationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStationModalLabel">Delete Station</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="deleteStationForm" method="post" action="deletestation.php">
                    <div class="modal-body">
                        <input type="hidden" name="station_id" id="deleteStationId">
                        <p>Are you sure you want to delete this station?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Populate edit modal with station data
        $('#editStationModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
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
        $('#deleteStationModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var stationId = button.data('id');

            // Set the value in the modal's hidden input
            var modal = $(this);
            modal.find('#deleteStationId').val(stationId);
        });
    </script>
</body>

</html>
