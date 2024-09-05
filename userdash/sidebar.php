<div class="sidebar">
    <div class="profile-section">
        <i class="fa fa-user profile-img"></i>
        <?php if (isset($user)): ?>
            <h3 class="username"><?php echo htmlspecialchars($user['username']); ?></h3>
        <?php else: ?>
            <h3 class="username">Guest</h3>
        <?php endif; ?>
    </div>
    <nav class="nav-links">
        <ul>
            <li><a href="profile.php">Profile Settings</a></li>
            <li><a href="updatepassword.php">Update Password</a></li>
            <li><a href="mybooking.php">My Booking</a></li>
            <li><a href="../Auth/logout.php">Sign Out</a></li>
        </ul>
    </nav>
</div>
