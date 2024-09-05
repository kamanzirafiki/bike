<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      outline: none;
    }

    body {
      width: 100%;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-family: 'Arial', sans-serif;
      background-color: black;
    }

    .container {
      width: 90%;
      max-width: 1170px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      align-items: center;
      grid-gap: 60px;
      padding: 35px 0;
      flex-grow: 1; /* Ensures the container takes up the remaining space between header and footer */
    }

    .contentLeft {
      width: 100%;
      height: 450px;
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 5px 10px 10px rgba(0, 0, 0, 0.15);
    }

    .contentLeft img {
      position: absolute;
      top: 50%;
      left: 50%;
      width: auto;
      height: 100%;
      transform: translate(-50%, -50%);
      object-fit: contain;
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }

    .contentLeft img:nth-child(1) {
      animation: slideShow 16s infinite;
      opacity: 1;
    }

    .contentLeft img:nth-child(2) {
      animation: slideShow 16s infinite 4s;
    }

    .contentLeft img:nth-child(3) {
      animation: slideShow 16s infinite 8s;
    }

    .contentLeft img:nth-child(4) {
      animation: slideShow 16s infinite 12s;
    }

    @keyframes slideShow {
      0%, 20% {
        opacity: 1;
      }
      25%, 100% {
        opacity: 0;
      }
    }

    .contentRight .content {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
      margin-bottom: 20%;
    }

    .contentRight .content h4 {
      font-size: 22px;
      font-weight: bold;
      color: #fff;
    }

    .contentRight .content h2 {
      font-size: 40px;
      color: #fff;
      font-family: "Arial";
    }

    .contentRight .content p {
      font-size: 16px;
      color: #fff;
      font-family: "Arial", sans-serif;
      line-height: 28px;
      padding-bottom: 10px;
    }

    .contentRight .content a {
      display: inline-block;
      text-decoration: none;
      font-size: 16px;
      letter-spacing: 1px;
      padding: 13px 30px;
      color: #fff;
      background: #a4aba6;
      border-radius: 8px;
      user-select: none;
    }

    .contentRight .content a:hover {
      background-color: #8f8989;
    }

    @media (max-width: 768px) {
      .container {
        grid-template-columns: 1fr;
      }

      .contentLeft {
        height: 300px;
      }
    }

    #feedback-section {
      position: relative; /* Ensure it's positioned relative */
      z-index: 10; /* Higher value to bring it above other elements */
    }

    /* Modal Styles */
    .modal {
      display: none; /* Hidden by default */
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.6); /* Slightly darker background for the modal overlay */
      backdrop-filter: blur(5px); /* Optional: Add a blur effect to the background */
    }

    .modal-content {
      background-color: #fff;
      margin: 15% auto;
      padding: 20px;
      border-radius: 10px;
      width: 40%; /* Reduced width */
      max-width: 500px; /* Optional: Set a maximum width */
      animation: shake 0.5s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Optional: Add a shadow for a cool effect */
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
      background-color: #4CAF50; /* Green background */
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
      background-color: #f44336; /* Red background for cancel */
    }

    .modal-buttons button:hover {
      background-color: #45a049; /* Darker green */
    }

    .modal-buttons button.cancel:hover {
      background-color: #c62828; /* Darker red for cancel */
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="contentLeft">
      <img src="../images/bike2.jpg" alt="Bike Image 1">
      <img src="../images/bike3.jpg" alt="Bike Image 2">
      <img src="../images/scoo8.jpg" alt="Scooter Image 1">
      <img src="../images/scoo7.jpg" alt="Scooter Image 2">
    </div>
    <div class="contentRight">
      <div class="content">
        
        <h2>Mission</h2>
        <p>Our mission is to provide easy and reliable access to bikes and scooters for everyone. We aim to become the go-to platform for affordable and convenient mobility solutions. By simplifying the rental process, we empower users to explore cities and destinations with freedom and ease.</p>

        <!-- Vision Section -->
        <h2>Vision</h2>
        <p>Our vision is to build a sustainable future by promoting eco-friendly transportation options. We strive to reduce our carbon footprint by offering efficient, green mobility solutions. We envision a world where bikes and scooters are the primary mode of transport in urban settings.</p>

        <a href="bike.php">Book Now</a>
      </div>
    </div>
  </div>

  

  <?php include '../includes/footer.php'; ?>
</body>
</html>
