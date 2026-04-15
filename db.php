<?php
// Database configuration — InfinityFree Hosting
$host     = "sql112.infinityfree.com";
$username = "if0_41645505";
$password = "smit10000";        // ← paste your actual password here
$dbname   = "if0_41645505_epiz_12345678_gym_db"; // ← use the exact name shown in your panel

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
