<?php
include 'db.php';
$blog_res = $conn->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
    <link rel="stylesheet" href="style.css?v=7">
    <script src="script.js"></script>
    <style>
    </style>
</head>
<body>
  
    <?php include 'header.php'; ?>
        <div class="blog-content">
            <h1>FROM OUR BLOG</h1>
            <p class="blog-text">List of all news and events that take place related to us</p>
        </div>

    <div class="container">
        <?php if ($blog_res && $blog_res->num_rows > 0): ?>
            <?php while($blog = $blog_res->fetch_assoc()): ?>
            <div class="card">
              <img src="<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" onerror="this.src='images/ground.avif'">
              <div class="card-text">
                <div class="category"><?= htmlspecialchars(strtoupper($blog['category'])) ?></div>
                <div class="title"><?= htmlspecialchars(strtoupper($blog['title'])) ?></div>
              </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; width:100%;">No blog posts available at the moment.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    
   
    
</body>
</html>
