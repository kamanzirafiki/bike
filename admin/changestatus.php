<?php
session_start();
include '../db_connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
    header("Location: log.php");
    exit;
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];

    // Determine the new status
    $new_status = ($current_status === 'Activate') ? 1 : 0;

    // Update the user's status in the database
    $sql = "UPDATE users SET is_active = :is_active WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':is_active' => $new_status,
        ':user_id' => $user_id
    ]);

   
    header("Location: viewUser.php");
    exit;
}
?>
