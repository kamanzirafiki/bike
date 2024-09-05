<?php include './userdash/header.php'; ?>
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
  </style>
</head>
<body>
  <div class="container">
    <div class="contentLeft">
      <img src="images/bike2.jpg" alt="Bike Image 1">
      <img src="images/bike3.jpg" alt="Bike Image 2">
      <img src="images/scoo8.jpg" alt="Scooter Image 1">
      <img src="images/scoo7.jpg" alt="Scooter Image 2">
    </div>
    <div class="contentRight">
      <div class="content">
        <h2>About Us</h2>
        <p>We are The Bikes & Scooters Rental. The only 100% dedicated bikes and scooters rental booking website. Ever since, it has been our aim to make bike rental easier for everyone, everywhere. We focus on making bike and scooter rentals easier for you. We are the only dedicated bike and scooter rental site and will be able to offer you a solution to match your needs. Get in touch with us and ride 🚲 with us today! We provide affordable bike rates.</p>
        <a href="bike.php">Book Now</a>
      </div>
    </div>
  </div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
