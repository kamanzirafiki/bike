<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();


header("Location: log.php");
exit;
?>
