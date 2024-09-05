<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Approved Vehicles</title>
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

        td img {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100px;
        }

        .no-vehicles {
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

        .action-btns .btn-warning {
            background-color: #ffc107;
            border: none;
            color: #fff;
        }

        .action-btns .btn-warning:hover {
            background-color: #e0a800;
        }

        .action-btns .btn-primary {
            background-color: #007bff;
            border: none;
            color: #fff;
        }

        .action-btns .btn-primary:hover {
            background-color: #0056b3;
        }

        .action-btns .btn-danger {
            background-color: #dc3545;
            border: none;
            color: #fff;
        }

        .action-btns .btn-danger:hover {
            background-color: #c82333;
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
        <h2>Approved Vehicles</h2>
        <?php
        // Database connection
        include '../db_connection.php';

        // Prepare and execute the query using PDO
        $sql = "SELECT bs.bike_scooter_id, bs.name, bs.model, bs.plate_number, bs.details, bs.type, bs.image, bs.price, v.company_name
                FROM bikes_scooters bs
                JOIN vendors v ON bs.vendor_id = v.vendor_id
                WHERE bs.approval_status = 'approved'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo '<table>';
            echo '<tr><th>Name</th><th>Model</th><th>Plate Number</th><th>Details</th><th>Type</th><th>Image</th><th>Price</th><th>Vendor</th><th>Actions</th></tr>';

            foreach ($result as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['model']) . '</td>';
                echo '<td>' . htmlspecialchars($row['plate_number']) . '</td>';
                echo '<td>' . htmlspecialchars($row['details']) . '</td>';
                echo '<td>' . htmlspecialchars($row['type']) . '</td>';
                echo '<td><img src="../vendor/uploads/' . htmlspecialchars($row['image']) . '" alt="Vehicle Image"></td>';
                echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                echo '<td>' . htmlspecialchars($row['company_name']) . '</td>';
                echo '<td class="action-btns">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editPriceModal" 
                                data-id="' . htmlspecialchars($row['bike_scooter_id']) . '" 
                                data-price="' . htmlspecialchars($row['price']) . '">
                            <i class="bi bi-pencil"></i> Edit Price
                        </button>
                        <a href="delvehicle.php?id=' . htmlspecialchars($row['bike_scooter_id']) . '" 
                           class="btn btn-danger" 
                           onclick="return confirmDeletion();">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                      </td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p class="no-vehicles">No approved vehicles available.</p>';
        }
        ?>
    </main>

    <!-- Modal for editing price -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceModalLabel">Edit Vehicle Price</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editPriceForm" method="post" action="updateprice.php">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="vehicleId">
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Price</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#editPriceModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var price = button.data('price');

            var modal = $(this);
            modal.find('#vehicleId').val(id);
            modal.find('#price').val(price);
        });

        function confirmDeletion() {
            return confirm('Are you sure you want to delete this vehicle?');
        }
    </script>
</body>

</html>
