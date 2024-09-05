<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html');


include 'db_connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $returnAddress = trim($_POST['returnAddress']);

    try {
        
        $stmt = $pdo->prepare("SELECT * FROM stations WHERE address = :address");
        $stmt->bindParam(':address', $returnAddress);
        $stmt->execute();

        
        if ($stmt->rowCount() > 0) {
            
            $station = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $message = "Return successful at " . htmlspecialchars($station['name']) . "!";
            $valid = 'true';
        } else {
            
            $message = "Invalid return location. Please choose a valid drop-off point.";
            $valid = 'false';
        }

        
        header("Location: return_vehicle.php?message=" . urlencode($message) . "&valid=" . $valid);
        exit();
    } catch (PDOException $e) {
        echo "Query failed: " . $e->getMessage();
        exit;
    }
}
?>
