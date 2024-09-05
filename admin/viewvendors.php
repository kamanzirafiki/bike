<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Database connection
include '../db_connection.php';

// Prepare and execute the query using PDO
$sql = "SELECT 
            vendor_id,
            company_name,
            email,
            phone,
            created_at,
            updated_at
        FROM vendors
        ORDER BY company_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Vendors</title>
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

        .no-vendors {
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
        <h2>View Vendors</h2>

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
        if (count($vendors) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover" id="vendorsTable">';
            echo '<thead><tr><th>#</th><th>Company Name</th><th>Email</th><th>Phone</th><th>Created At</th><th>Updated At</th></tr></thead>';
            echo '<tbody>';

            $counter = 1;
            foreach ($vendors as $vendor) {
                echo '<tr>';
                echo '<td>' . $counter . '</td>';
                echo '<td>' . htmlspecialchars($vendor['company_name']) . '</td>';
                echo '<td>' . htmlspecialchars($vendor['email']) . '</td>';
                echo '<td>' . htmlspecialchars($vendor['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($vendor['created_at']) . '</td>';
                echo '<td>' . htmlspecialchars($vendor['updated_at']) . '</td>';
                echo '</tr>';
                $counter++;
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p class="no-vendors">No vendors available.</p>';
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Search functionality for filtering the table
        $(document).ready(function() {
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#vendorsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>

</html>
