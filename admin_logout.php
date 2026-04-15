<?php
session_start();

// Destroy only the admin session variables, not visitor session
unset($_SESSION['adminLoggedIn']);
unset($_SESSION['adminUser']);

// If session is now completely empty, destroy it fully
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to admin login with confirmation
echo "<script>alert('Admin logged out successfully.'); window.location.href='admin_login.php';</script>";
exit;
?>
