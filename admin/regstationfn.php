<?php
include '../db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);

    if (!empty($name)) {
        try {
            // Check if a station with the same name already exists
            $check_sql = "SELECT * FROM stations WHERE name = :name";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->bindParam(':name', $name);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                $_SESSION['message'] = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> A station already exists.</div>';
            } else {
                $sql = "INSERT INTO stations (name, address) VALUES (:name, :address)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':address', $address);

                if ($stmt->execute()) {
                    $_SESSION['message'] = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Station registered successfully!</div>';
                } else {
                    $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Failed to register the station. Please try again.</div>';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error: ' . $e->getMessage() . '</div>';
        }
    } else {
        $_SESSION['message'] = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please provide a station name.</div>';
    }

    header("Location: regstation.php");
    exit();
}
