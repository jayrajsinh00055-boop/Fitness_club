    <?php
session_start();
// Routing Guard: Send already logged-in members to their dashboard
if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
    echo "<script>window.location.href='homepage.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
    <html>
    <head>
        

        <title>Home</title>
        <link rel="stylesheet" href="style.css?v=9">
       
        <script src="script.js"></script>
        <style>
           
        </style>
    </head>
    <body>
    <?php include 'header.php'; ?>
        <main class="image-container">
            <img src="images/bg4.jpg" alt="Gym Banner" class="hero-bg-img">
            <div class="hero-overlay"></div>
            <div class="center-text">
                <h1>Welcome to Our Gym</h1>
                <p>Join us for a healthier lifestyle!</p>
                <button class="home-button"><a href="Page4.php" style="text-decoration: none; color: #000;">Get Started</a></button>
            </div>
        </main>

        
      

        <!-- Testimonials Section -->
        <section class="features-section" style="background-color: white; margin-top: 0; padding-top: 20px;">
            <h2 style="font-size: 2.5rem; text-transform: uppercase;">What Our Members Say</h2>
            <div class="features-grid" style="max-width: 1100px; margin: 0 auto; gap: 30px;">
                <div class="feature-card" style="text-align: left; padding: 40px 30px; animation: fadeInUp 1.5s ease;">
                    <div style="display: flex; color: #f5b301; font-size: 1.5rem; margin-bottom: 20px;">
                        ★★★★★
                    </div>
                    <p style="font-style: italic; margin-bottom: 25px; font-size: 1.1rem; color: #444;">"Joining this gym was the best decision I've made. The trainers are incredibly supportive, and the equipment is top-notch. I've seen amazing results in just a few months!"</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background-color: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555; font-size: 1.2rem;">SJ</div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.1rem; color: #222;">Sarah Jenkins</h4>
                            <span style="font-size: 0.9rem; color: #777;">Member since 2022</span>
                        </div>
                    </div>
                </div>
                <div class="feature-card" style="text-align: left; padding: 40px 30px; animation: fadeInUp 1.7s ease;">
                    <div style="display: flex; color: #f5b301; font-size: 1.5rem; margin-bottom: 20px;">
                        ★★★★★
                    </div>
                    <p style="font-style: italic; margin-bottom: 25px; font-size: 1.1rem; color: #444;">"The Group Fitness classes here are unmatched. The energy is fantastic, and everyone is so welcoming. It really makes working out feel like fun rather than a chore."</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background-color: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555; font-size: 1.2rem;">MR</div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.1rem; color: #222;">Mike Rossi</h4>
                            <span style="font-size: 0.9rem; color: #777;">Premium Member</span>
                        </div>
                    </div>
                </div>
                <div class="feature-card" style="text-align: left; padding: 40px 30px; animation: fadeInUp 1.9s ease;">
                    <div style="display: flex; color: #f5b301; font-size: 1.5rem; margin-bottom: 20px;">
                        ★★★★★
                    </div>
                    <p style="font-style: italic; margin-bottom: 25px; font-size: 1.1rem; color: #444;">"I love the 24/7 access. The facility is always clean and well-maintained. Getting to work out on my own schedule is a huge plus for my busy lifestyle."</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background-color: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555; font-size: 1.2rem;">DC</div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.1rem; color: #222;">David Chen</h4>
                            <span style="font-size: 0.9rem; color: #777;">Member since 2023</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Call to Action Banner -->
        <section style="background: linear-gradient(135deg, #111, #333); color: white; padding: 80px 20px; text-align: center; border-radius: 12px; max-width: 1100px; margin: 0 auto 80px auto; box-shadow: 0 15px 40px rgba(0,0,0,0.3);">
            <h2 style="font-size: 3rem; margin-bottom: 20px; color: white;">Ready to Transform Your Life?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 35px; color: #ddd; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.6;">Join our community today and take the first step towards a stronger, healthier version of yourself. Your journey starts here, customized to your goals.</p>
            <a href="Page4.php" style="display: inline-block; background-color: white; color: black; padding: 18px 45px; text-decoration: none; font-size: 1.2rem; font-weight: bold; border-radius: 50px; text-transform: uppercase; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 15px rgba(255,255,255,0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(255,255,255,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255,255,255,0.2)';">Become a Member Today</a>
        </section>

    <?php include 'footer.php'; ?>
    
        
        
    </body>
</html>
