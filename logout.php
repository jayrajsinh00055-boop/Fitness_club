<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with an alert
echo "<script>alert('You have been logged out.'); window.location.href='Page7.php';</script>";
exit;
?>
