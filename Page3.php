<?php
include 'db.php';
$services_res = $conn->query("SELECT * FROM services ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Services</title>
    <link rel="stylesheet" href="style.css?v=7">
    <script src="script.js"></script>
    <style>
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="blog-content">
        <h1>SERVICES</h1>
        <p class="blog-text">World-class facilities to help you achieve your fitness goals</p>
    </div>
    <div class="timing-services">
        <div class="timing-inner">
            <p>Hours: Monday - Friday 6AM - 10PM, Weekends 8AM - 8PM</p>
            <p>Phone: (555) 123-4567</p>
        </div>
    </div>
    
    <h2 class="services-title">Available Services</h2>
    
    <div class="services-grid">
        <?php if ($services_res && $services_res->num_rows > 0): ?>
            <?php while($srv = $services_res->fetch_assoc()): ?>
            <div class="service-item">
                <div style="font-size: 2.5rem; margin-bottom: 12px;"><?= htmlspecialchars($srv['icon']) ?></div>
                <h3><?= htmlspecialchars($srv['name']) ?></h3>
                <p><?= htmlspecialchars($srv['description']) ?></p>
                <p class="price"><?= htmlspecialchars($srv['price']) ?></p>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; width:100%; padding: 40px;">No services available at the moment. Check back soon!</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
