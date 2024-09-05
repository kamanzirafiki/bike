<!-- userdash/dashboard_card.php -->
<div class="card dashboard-content">
    <h4>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h4>
    <p>Here’s a summary of your recent activities:</p>

    <h5>Recent Bookings</h5>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Bike/Scooter</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch user's recent bookings
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = :user_id ORDER BY booking_date DESC LIMIT 5");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($bookings) {
                foreach ($bookings as $booking) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($booking['booking_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['bike_or_scooter']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['booking_date']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['status']) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="4" class="text-center">No recent bookings found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
