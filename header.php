<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true;
?>
   <header>
        <div class="header-content">
            <div class="logo-area">
                <h1 class="gym-text">FITNESS CLUB</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="indexpage.php" id="nav-home-link">Home</a></li>
                    <li><a href="Page2.php">Blog</a></li>
                    <li><a href="Page3.php">Services</a></li>
                    
                    <?php if(!$isLoggedIn): ?>
                        <li><a href="Page4.php">Register</a></li>
                        <li><a href="Page7.php">Login</a></li>
                    <?php else: ?>
                        <li><a href="logout.php">🚪 Logout</a></li>
                    <?php endif; ?>
                    
                    <li><a href="Page6.php">Contact</a></li>
                    <li><a href="Page5.php">About us</a></li>
                    <li><a href="admin_login.php" style="color:#333; font-weight:700;">⚙ Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
