<?php
include 'db.php';
$conn->query("ALTER TABLE members ADD COLUMN password VARCHAR(255) NOT NULL AFTER email");
echo "Migration complete! Password column added.";
?>
