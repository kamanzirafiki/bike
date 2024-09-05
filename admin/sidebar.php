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

<div class="col-md-3 col-lg-2 px-0 position-fixed h-100 bg-dark shadow-sm sidebar" id="sidebar">
<h1 class="fas fa-bicycle text-primary d-flex my-4 justify-content-center"></h1> 
    <div class="list-group rounded-0">
        <a href="index.php" class="list-group-item list-group-item-action active border-0 d-flex align-items-center">
            <span class="fas fa-border-all"></span>
            <span class="ml-2">Dashboard</span>
        </a>
        <button class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"
                data-toggle="collapse" data-target="#bike-scooter-collapse">
            <div>
                <span class="fas fa-bicycle"></span>
                <span class="ml-2">Bikes & Scooters</span>
            </div>
            <span class="fas fa-caret-down"></span>
        </button>
        <div id="bike-scooter-collapse" class="collapse">
            <a href="approve.php" class="list-group-item list-group-item-action border-0">
                <span class="fas fa-check-circle"></span>
                <span class="ml-2">Approve Vehicles</span>
            </a>
            <a href="viewvehicle.php" class="list-group-item list-group-item-action border-0">
                <span class="fas fa-eye"></span>
                <span class="ml-2">View Vehicles</span>
            </a>
        </div>
        <button class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"
                data-toggle="collapse" data-target="#location-collapse">
            <div>
                <span class="fas fa-location-dot"></span>
                <span class="ml-2">Stations</span>
            </div>
            <span class="fas fa-caret-down"></span>
        </button>
        <div id="location-collapse" class="collapse">
            <a href="viewstation.php" class="list-group-item list-group-item-action border-0">
                <span class="fas fa-eye"></span>
                <span class="ml-2">View Stations</span>
            </a>
            <a href="regstation.php" class="list-group-item list-group-item-action border-0">
                <span class="fas fa-plus-circle"></span>
                <span class="ml-2">Add Stations</span>
            </a>
        </div>
        <a href="viewbooking.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fas fa-book"></span>
            <span class="ml-2">Manage Booking</span>
        </a>
        <a href="viewcontactus.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fas fa-envelope"></span>
            <span class="ml-2">Manage ContactUs</span>
        </a>
        <a href="viewuser.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fas fa-user"></span>
            <span class="ml-2">Registered Users</span>
        </a>
        <a href="viewvendors.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fas fa-user-tie"></span>
            <span class="ml-2">Manage Vendors</span>
        </a>
        <a href="viewsubs.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fas fa-user-check"></span>
            <span class="ml-2">Manage Subscribers</span>
        </a>
        <a href="viewfeedback.php" class="list-group-item list-group-item-action border-0 align-items-center">
            <span class="fa-solid fa-comment"></span>
            <span class="ml-2">Feedback</span>
        </a>
    </div>
</div>
<div id="sidebar-overlay" class="overlay"></div>
