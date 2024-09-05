<?php include 'header.php'; ?>

<style>
  .hero-section {
    position: relative;
    background-image: url('../images/bike2.jpg');
    background-size: cover;
    background-position: center;
    height: 80vh;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  #feedback-section {
    position: relative; 
    z-index: 10; 
  }
  
  .modal {
    display: none; 
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6); 
    backdrop-filter: blur(5px); 
  }
  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 40%; 
    max-width: 500px; 
    animation: shake 0.5s;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2); 
  }
  @keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    50% { transform: translateX(10px); }
    75% { transform: translateX(-10px); }
    100% { transform: translateX(0); }
  }
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }
  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
  .modal-buttons {
    text-align: center;
    margin-top: 20px;
  }
  .modal-buttons button {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 5px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }
  .modal-buttons button.cancel {
    background-color: #f44336; 
  }
  .modal-buttons button:hover {
    background-color: #45a049; 
  }
  .modal-buttons button.cancel:hover {
    background-color: #c62828; /* Darker red for cancel */
  }
</style>

<div class="hero-section">
  <div class="hero-content">
    <h3>FIND YOUR PERFECT BIKE & SCOOTER</h3>
    <p>We have more Bike and Scooter for you to choose.</p>
    <a href="<?php echo isset($_SESSION['username']) ? 'bike.php' : '../register/index.php'; ?>" class="btn">
      Get Started <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
    </a> 
  </div>
</div>

<?php include 'modal.php'; ?>
<?php include '../includes/footer.php'; ?>

<script src="modal.js"></script>
