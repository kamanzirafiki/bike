<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .sidebar {
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform 0.4s ease-out;
            background-color: #343a40;
            min-height: 100vh;
        }

        .sidebar .list-group-item {
            border-radius: 0;
            border: 0;
            background-color: #343a40;
            color: #f8f9fa;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar .list-group-item:hover {
            background-color: #495057;
            color: #ffffff;
        }

        .sidebar .list-group-item.active {
            background-color: #007bff;
            color: #ffffff;
        }

        .overlay {
            display: none;
            background-color: rgba(0, 0, 0, 0.45);
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

        body {
            font-family: 'Barlow', sans-serif;
        }

        a:hover {
            text-decoration: none;
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
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div id="sidebar-overlay" class="overlay"></div>
            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
                <?php include 'header.php'; ?>
                <div class="container-fluid mt-3">
                    <h1>Welcome to the Vendor Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

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
