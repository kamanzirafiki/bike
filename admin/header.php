<?php
// Include your database connection
include '../db_connection.php';

try {
    // Query to get counts for different types of notifications (assuming 'type' is used to categorize notifications)
    $query = "
        SELECT
            COUNT(CASE WHEN type = 'registration' AND status = 'unread' THEN 1 END) AS registration_count,
            COUNT(CASE WHEN type = 'booking' AND status = 'unread' THEN 1 END) AS booking_count,
            COUNT(CASE WHEN type = 'vendor' AND status = 'unread' THEN 1 END) AS vendor_count,
            COUNT(CASE WHEN type = 'approval' AND status = 'unread' THEN 1 END) AS approval_count
        FROM notifications
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $registrationCount = $row ? $row['registration_count'] : 0;
    $bookingCount = $row ? $row['booking_count'] : 0;
    $vendorCount = $row ? $row['vendor_count'] : 0;
    $approvalCount = $row ? $row['approval_count'] : 0;

    $totalNotifications = $registrationCount + $bookingCount + $vendorCount + $approvalCount;

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
    $registrationCount = $bookingCount = $vendorCount = $approvalCount = $totalNotifications = 0;
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
                        <?php if ($totalNotifications > 0): ?>
                            <span class="badge badge-danger position-absolute top-0 start-100 translate-middle badge-sm">
                                <?php echo htmlspecialchars($totalNotifications); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right bg-light border shadow" aria-labelledby="notificationsDropdown">
                        <?php if ($totalNotifications > 0): ?>
                            <?php if ($registrationCount > 0): ?>
                                <a class="dropdown-item" href="registrations.php">
                                    <?php echo "You have $registrationCount new registration(s)"; ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($bookingCount > 0): ?>
                                <a class="dropdown-item" href="bookings.php">
                                    <?php echo "You have $bookingCount new booking(s)"; ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($vendorCount > 0): ?>
                                <a class="dropdown-item" href="vendors.php">
                                    <?php echo "You have $vendorCount new vendor(s)"; ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($approvalCount > 0): ?>
                                <a class="dropdown-item" href="approve.php">
                                    <?php echo "You have $approvalCount vehicle(s) pending approval"; ?>
                                    <?php if ($vendorName): ?>
                                        <br>Vendor: <?php echo htmlspecialchars($vendorName); ?>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a class="dropdown-item" href="#">No new notifications</a>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                       id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                        <span class="fas fa-user"></span> 
                        <span class="ml-2">Profile</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
body {
    font-family: "Arial", sans-serif;
}

.badge-sm {
    font-size: 10px;
}

.dropdown-menu {
    max-width: 500px;
}
</style>
