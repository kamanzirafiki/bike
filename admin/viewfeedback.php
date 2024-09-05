<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Include your database connection
include '../db_connection.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
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

        .no-feedback {
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
    </style>
</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Include sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main content area -->
    <main>
        <h2>View Feedback</h2>

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

            <div class="form-group">
                <label for="search">Search:</label>
                <input type="text" id="search" class="form-control" placeholder="Search feedback...">
            </div>
        </div>

        <?php
        try {
            // Prepare the SQL query to fetch feedback with usernames
            $stmt = $pdo->prepare("
                SELECT 
                    f.id, 
                    f.feedback_text, 
                    f.rating, 
                    f.created_at, 
                    u.username 
                FROM 
                    feedback f
                JOIN 
                    users u ON f.user_id = u.user_id
                ORDER BY 
                    f.created_at DESC
            ");

            // Execute the query
            $stmt->execute();

            // Fetch all the results
            $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($feedbacks) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-hover" id="feedbackTable">';
                echo '<thead><tr><th>Username</th><th>Feedback</th><th>Rating</th><th>Submitted At</th></tr></thead>';
                echo '<tbody>';

                foreach ($feedbacks as $feedback) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($feedback['username']) . '</td>';
                    echo '<td>' . htmlspecialchars($feedback['feedback_text']) . '</td>';
                    echo '<td>' . htmlspecialchars($feedback['rating']) . '</td>';
                    echo '<td>' . htmlspecialchars($feedback['created_at']) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p class="no-feedback">No feedback available.</p>';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
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
                $("#feedbackTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>

</html>
