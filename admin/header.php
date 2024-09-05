<!-- header.php -->
<?php
// Include your database connection
include '../db_connection.php';

try {
    // Prepare and execute query to get the count of bikes/scooters that need approval
    $query = "SELECT COUNT(*) AS pending_vehicles FROM bikes_scooters WHERE approval_status = 'pending'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $pendingCount = $row ? $row['pending_vehicles'] : 0;

    // Prepare and execute query to get the details of one of the pending bikes/scooters
    $notificationQuery = "
        SELECT v.company_name 
        FROM bikes_scooters bs
        JOIN vendors v ON bs.vendor_id = v.vendor_id
        WHERE bs.approval_status = 'pending'
        LIMIT 1
    ";
    $stmt = $pdo->prepare($notificationQuery);
    $stmt->execute();
    $notificationRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $vendorName = $notificationRow ? $notificationRow['company_name'] : '';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $pendingCount = 0;
    $vendorName = '';
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <button id="sidebar-toggle" class="btn btn-link d-block d-md-none">
            <span class="fas fa-bars"></span> <!-- Updated to Font Awesome -->
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fas fa-bell"></span> <!-- Updated to Font Awesome -->
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge badge-danger position-absolute top-0 start-100 translate-middle badge-sm">
                                <?php echo htmlspecialchars($pendingCount); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right bg-light border shadow" aria-labelledby="notificationsDropdown">
                        <?php if ($pendingCount > 0): ?>
                            <a class="dropdown-item" href="approve.php">
                                <?php echo "You have $pendingCount bike(s)/scooter(s) pending approval"; ?>
                                <?php if ($vendorName): ?>
                                    <br>Vendor: <?php echo htmlspecialchars($vendorName); ?>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a class="dropdown-item" href="#">No new notifications</a>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                       id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                        <span class="fas fa-user"></span> <!-- Updated to Font Awesome -->
                        <span class="ml-2">Profile</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href="#">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>

.badge-sm {
    font-size:10px; 
    
}


.dropdown-menu {
    max-width: 500px; 
}
</style>
