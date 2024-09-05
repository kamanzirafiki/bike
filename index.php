<?php include './userdash/header.php'; ?>
<style>
  .hero-section {
            position: relative;
            background-image: url('./images/bike2.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            color: white;
        }
</style>

<div class="hero-section">
  <div class="hero-content">
    <h3>FIND YOUR PERFECT BIKE & SCOOTER</h3>
    <p>We have more Bike and Scooter for you to choose.</p>
    <a href="<?php echo isset($_SESSION['username']) ? 'bike.php' : './register/'; ?>" class="btn">
      Get Started <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
    </a> 
  </div>
</div>

<?php include 'includes/footer.php'; ?>
