<?php include 'includes/header.php'; ?>

<div class="hero-section">
  <div class="hero-content">
    <h3>FIND YOUR PERFECT BIKE & SCOOTER</h3>
    <p>We have more Bike and Scooter for you to choose.</p>
    <a href="<?php echo isset($_SESSION['username']) ? './booking.php' : './register/index.php'; ?>" class="btn">
      Get Started <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
    </a> 
  </div>
</div>

<?php include 'includes/footer.php'; ?>
