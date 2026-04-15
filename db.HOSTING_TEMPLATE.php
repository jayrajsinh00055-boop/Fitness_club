<?php
// ============================================================
// HOSTING TEMPLATE - db.php
// Replace the values below with your InfinityFree credentials
// from: Panel → MySQL Databases → View Details
// ============================================================

$host     = "YOUR_DB_HOST";       // e.g. sql123.epizy.com
$username = "YOUR_DB_USERNAME";   // e.g. epiz_12345678
$password = "YOUR_DB_PASSWORD";   // password you set
$dbname   = "YOUR_DB_NAME";       // e.g. epiz_12345678_gym_db

// ============================================================
// DO NOT EDIT BELOW THIS LINE
// ============================================================
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Auto-setup Database Tables
$tables = [
    "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        age INT,
        gender VARCHAR(10),
        joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'active'
    )",
    "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200),
        message TEXT,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'unread'
    )",
    "CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT NOT NULL,
        image VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        icon VARCHAR(50) NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT NOT NULL,
        price VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $tableSql) {
    if ($conn->query($tableSql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }
}

// Default Admin Account (username: admin | password: admin123)
$admin_check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
if ($admin_check->num_rows == 0) {
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admin_users (username, password) VALUES ('admin', '$hashed_password')");
}

// Seed default Blog Posts if empty
$blog_check = $conn->query("SELECT COUNT(*) AS count FROM blog_posts");
$blog_row = $blog_check->fetch_assoc();
if ($blog_row['count'] == 0) {
    $seeded_blogs = [
        "('Khabib', 'New UFC Training Is Also In Our Gym', 'Experience the legendary UFC training methodology.', 'images/khabib.webp')",
        "('Yoga', 'Beautiful Yoga Room Is In Our Gym', 'Discover our newly designed yoga studio.', 'images/yoga room.webp')",
        "('Ground', 'Just New Ground Added For Running', 'We added a brand-new running track.', 'images/ground.avif')"
    ];
    foreach ($seeded_blogs as $blog) {
        $conn->query("INSERT INTO blog_posts (category, title, description, image) VALUES $blog");
    }
}

// Seed default Services if empty
$service_check = $conn->query("SELECT COUNT(*) AS count FROM services");
$service_row = $service_check->fetch_assoc();
if ($service_row['count'] == 0) {
    $seeded_services = [
        "('🏃', 'Personal Training', 'One-on-one training sessions tailored to you.', '\$60/hour')",
        "('🧘', 'Group Classes', 'Yoga, Pilates, Zumba, and High-Energy Spinning.', '\$15/class')",
        "('🏋️', 'Weight Training', 'Full access to dynamic free weights and equipment.', 'Included in membership')",
        "('🚴', 'Cardio Area', 'Premium treadmills, bikes, and elliptical machines.', 'Included in membership')",
        "('🏊', 'Swimming Pool', 'Temperature-controlled 25-meter indoor pool.', 'Included in membership')",
        "('🥗', 'Nutrition Counseling', 'Personalized meal plans with expert coaches.', '\$40/session')"
    ];
    foreach ($seeded_services as $srv) {
        $conn->query("INSERT INTO services (icon, name, description, price) VALUES $srv");
    }
}

// Fix any NULL column values
$conn->query("UPDATE services SET icon='🏋️' WHERE icon IS NULL OR icon=''");
$conn->query("UPDATE services SET name='(Untitled)' WHERE name IS NULL OR name=''");
$conn->query("UPDATE services SET description='' WHERE description IS NULL");
$conn->query("UPDATE services SET price='N/A' WHERE price IS NULL OR price=''");
$conn->query("UPDATE blog_posts SET category='General' WHERE category IS NULL OR category=''");
$conn->query("UPDATE blog_posts SET title='(Untitled)' WHERE title IS NULL OR title=''");
$conn->query("UPDATE blog_posts SET description='' WHERE description IS NULL");
$conn->query("UPDATE blog_posts SET image='images/ground.avif' WHERE image IS NULL OR image=''");
?>
