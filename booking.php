<?php
include 'db_connection.php'; 
include './userdash/header.php';


if (!isset($_GET['bike_scooter_id'])) {
    echo "Vehicle not specified.";
    exit;
}

$bike_scooter_id = $_GET['bike_scooter_id'];


if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}

$user_id = $_SESSION['user_id']; 

try {
    
    $sql = "SELECT * FROM bikes_scooters WHERE bike_scooter_id = :bike_scooter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bike_scooter_id', $bike_scooter_id, PDO::PARAM_INT);
    $stmt->execute();
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        echo "Vehicle not found.";
        exit;
    }

    
    $pickup_sql = "SELECT DISTINCT station_id, name FROM stations";
    $pickup_stmt = $pdo->query($pickup_sql);
    $pickup_stations = $pickup_stmt->fetchAll(PDO::FETCH_ASSOC);

    $dropoff_sql = "SELECT DISTINCT station_id, name FROM stations";
    $dropoff_stmt = $pdo->query($dropoff_sql);
    $dropoff_stations = $dropoff_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Form</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Arial", sans-serif;
    }

    .form-main {
      background: linear-gradient(to bottom, #00000024, #00000024),
        url(images/bike2.jpg) no-repeat center;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .form-container {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .main-wrapper,
    .description-wrapper {
      border-radius: 10px;
      padding: 25px;
      backdrop-filter: blur(8px);
      background-color: #ffffff85;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .main-wrapper {
      width: 48%;
      min-height: 400px;
    }

    .description-wrapper {
      width: 48%;
      color: #fff;
      font-size: 16px;
      line-height: 1.6;
      margin-top: 0;
    }

    @media screen and (max-width: 991px) {
      .main-wrapper,
      .description-wrapper {
        width: 100%;
      }
    }

    @media screen and (max-width: 767px) {
      .form-container {
        flex-direction: column;
        gap: 20px;
      }
    }

    .form-head {
      font-size: 30px;
      color: #fff;
      line-height: 40px;
      font-weight: 600;
      text-align: left;
      margin: 0 0 25px;
      position: relative;
      padding-bottom: 15px;
    }

    .form-head::after {
      content: "";
      display: block;
      width: 90%;
      height: 4px;
      background: #fff;
      position: absolute;
      left: 50%;
      bottom: 0;
      transform: translateX(-50%);
    }

    .form-wrapper {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-input,
    .form-textarea,
    .form-select {
      padding: 20px 25px 15px;
      width: 100%;
      border: 1px solid #fff;
      border-radius: 5px;
      background: transparent;
      outline: none;
      font-size: 20px;
      line-height: 30px;
      font-weight: 400;
      box-sizing: border-box;
      color: #fff;
    }

    .form-textarea {
      resize: vertical;
      min-height: 120px;
      line-height: 1.5;
    }

    .form-label,
    .form-textarea-label {
      position: absolute;
      left: 25px;
      top: 60%;
      transform: translateY(-90%);
      pointer-events: none;
      transition: 0.3s;
      margin: 0;
      font-size: 14px;
      line-height: 20px;
      font-weight: 500;
      color: #fff;
    }

    .form-textarea-label {
      top: 20%;
      transform: translateY(-75%);
    }

    .form-input:valid ~ .form-label,
    .form-input:focus ~ .form-label,
    .form-textarea:valid ~ .form-textarea-label,
    .form-textarea:focus ~ .form-textarea-label {
      top: 1%;
      transform: translateY(-90%);
      font-size: 13px;
      line-height: 23px;
    }

    .btn-wrap {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 16px 0 0;
    }

    .btn-wrap button {
      padding: 0 32px;
      font-size: 18px;
      line-height: 48px;
      border: 1px solid transparent;
      font-weight: 600;
      border-radius: 6px;
      transition: all 0.5s ease;
      background-color: #A4ABA6;
      cursor: pointer;
      box-shadow: 0 0 5px 5px #00000020;
      color: #fff;
    }

    .btn-wrap button:hover {
      border: 1px solid #000;
      background: transparent;
    }

    /* Alert message styling */
    .alert {
      padding: 15px;
      background-color: #4CAF50; /* Green */
      color: white;
      margin-bottom: 15px;
      border-radius: 5px;
      text-align: center;
    }

    .alert.error {
      background-color: #f44336; /* Red */
    }

    .alert.success {
      background-color: #4CAF50; /* Green */
    }
  </style>
</head>
<body>
  <div class="form-main">
    <div class="form-container">
      <div class="main-wrapper">
        <h2 class="form-head">Book Your Ride</h2>
        <?php if (isset($alert_message)): ?>
          <div class="alert <?php echo htmlspecialchars($alert_class); ?>">
            <?php echo htmlspecialchars($alert_message); ?>
          </div>
        <?php endif; ?>
        <form action="book.php" method="post" class="form-wrapper">
          <input type="hidden" name="bike_scooter_id" value="<?php echo htmlspecialchars($bike_scooter_id); ?>">

          <div class="mb-3">
            <select name="pickup_station" id="pickup_station" class="form-select" required>
              <option value="">Select Pickup Station</option>
              <?php foreach ($pickup_stations as $station) : ?>
                <option value="<?php echo htmlspecialchars($station['station_id']); ?>">
                  <?php echo htmlspecialchars($station['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <select name="dropoff_station" id="dropoff_station" class="form-select" required>
              <option value="">Select Dropoff Station</option>
              <?php foreach ($dropoff_stations as $station) : ?>
                <option value="<?php echo htmlspecialchars($station['station_id']); ?>">
                  <?php echo htmlspecialchars($station['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <input type="date" name="booking_date" id="booking_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" readonly>
          </div>

          <div class="mb-3">
            <input type="text" name="total_price" id="total_price" class="form-input" value="<?php echo htmlspecialchars($vehicle['price'] ?? ''); ?>" readonly>
          </div>

          <div class="btn-wrap">
            <button type="submit">Book Now</button>
          </div>
        </form>
      </div>

      <div class="description-wrapper">
        <h2 class="form-head">Vehicle Details</h2>      
        <p><strong>Name:</strong> <?php echo htmlspecialchars($vehicle['name']); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
        <p><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></p>
        <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($vehicle['details']); ?></p>
        <?php if (!empty($vehicle['image'])): ?>
          <img src="company/uploads/<?php echo htmlspecialchars($vehicle['image']); ?>" alt="Vehicle Image" style="width: 100%; height: auto; border-radius: 5px;">
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</body>
</html>
