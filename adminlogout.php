<?php
session_start();

// Destroy all session data
$_SESSION = array(); // Clear session array
session_destroy();   // Destroy the session

// Redirect to login page
header("Location: adminlogin.html");
exit;
?>
