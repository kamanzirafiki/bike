<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Vehicles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        h1, h2 {
            margin: 0;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
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
        }

        .actions {
            text-align: center;
        }

        .actions .btn {
            margin: 2px;
            font-size: 0.8em;
            display: inline-flex;
            align-items: center;
        }

        .actions .btn-sm {
            padding: 5px 10px;
        }

        .no-vehicles {
            text-align: center;
            padding: 20px;
            font-size: 1.2em;
            color: #555;
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
        <h2>Pending Vehicle Approvals</h2>
        <?php
        // Database connection
        include '../db_connection.php';

        // Prepare and execute the query using PDO
        $sql = "SELECT bs.bike_scooter_id, bs.name, bs.model, bs.plate_number, bs.details, bs.type, bs.image, v.company_name
                FROM bikes_scooters bs
                JOIN vendors v ON bs.vendor_id = v.vendor_id
                WHERE bs.approval_status = 'pending'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo '<table>';
            echo '<tr><th>Vendor</th><th>Name</th><th>Model</th><th>Plate Number</th><th>Details</th><th>Type</th><th>Image</th><th>Actions</th></tr>';

            foreach ($result as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['company_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['model']) . '</td>';
                echo '<td>' . htmlspecialchars($row['plate_number']) . '</td>';
                echo '<td>' . htmlspecialchars($row['details']) . '</td>';
                echo '<td>' . htmlspecialchars($row['type']) . '</td>';
                echo '<td><img src="../vendor/uploads/' . htmlspecialchars($row['image']) . '" alt="Vehicle Image" width="100"></td>';
                echo '<td class="actions">
                        <div class="btn-group">
                            <a href="approvefn.php?id=' . htmlspecialchars($row['bike_scooter_id']) . '&action=approve" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> <strong>Approve</strong>
                            </a>
                            <a href="approvefn.php?id=' . htmlspecialchars($row['bike_scooter_id']) . '&action=reject" class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> <strong>Reject</strong>
                            </a>
                        </div>
                      </td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p class="no-vehicles">No vehicles pending approval.</p>';
        }
        ?>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
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
