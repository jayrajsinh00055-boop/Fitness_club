<?php
session_start();
include 'db.php';

// If already logged in, redirect home smoothly
if(isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true){
    echo "<script>window.location.href='admin_panel.php';</script>";
    exit;
}
if(isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true){
    echo "<script>window.location.href='homepage.php';</script>";
    exit;
}

$error_message = '';

// Handle Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Verify member inside the "members" database
    $result = $conn->query("SELECT * FROM members WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Secure comparison using standard password_verify logic
        if (password_verify($password, $row['password'])) {
            $_SESSION['userLoggedIn'] = true;
            $_SESSION['userName'] = $row['name'];
            echo "<script>alert('Login successful!'); window.location.href='homepage.php';</script>";
            exit;
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "This email is not registered with us.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - FITNESS CLUB</title>
    <link rel="stylesheet" href="style.css?v=7">
    
    <style>
        body { font: 16px Arial, sans-serif; }
        b.error { color: red; font-size: 14px; display: none; }
    </style>
</head>
<body>
    <?php include 'header.php';?>

    <div class="blog-content">
        <h1>LOGIN</h1>
        <p class="blog-text">Welcome back to our gym</p>
    </div>

    <center>
        <div class="registration-form"><br>
            <form method="POST" action="Page7.php" id="loginForm">
                <h1>Login Form</h1><br>

                <?php if(!empty($error_message)): ?>
                <div style="color:red; margin-bottom:15px;">
                    <b><span><?php echo $error_message; ?></span></b>
                </div>
                <?php endif; ?>

                <input name="email" id="email" class="input" type="email" placeholder="Enter your Email"  autofocus><br>
                <b id="emailError" class="error"></b><br>

                <input name="password" id="password" class="input" type="password" placeholder="Enter your Password" ><br>
                <b id="passwordError" class="error"></b><br>

                <button type="submit" class="reg">Login</button><br><br>
            </form>
        </div><br><br>
    </center>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    let isValid = true;
    
    // Email Validation
    const email = document.getElementById('email').value.trim();
    const email_regex = /^[a-z0-9]+@[a-z0-9]+\.[a-z]{2,}$/;
    const emailError = document.getElementById('emailError');

    if (email === "") {
        emailError.style.display = "block";
        emailError.innerHTML = "Email is required";
        isValid = false;
    } else if (!email_regex.test(email)) {
        emailError.style.display = "block";
        emailError.innerHTML = "Invalid email format";
        isValid = false;
    } else {
        emailError.style.display = "none";
    }

    // Password Validation
    const password = document.getElementById('password').value;
    const passwordError = document.getElementById('passwordError');

    if (password === "") {
        passwordError.style.display = "block";
        passwordError.innerHTML = "Password is required";
        isValid = false;
    } else if (password.length < 6) {
        passwordError.style.display = "block";
        passwordError.innerHTML = "Password must be at least 6 characters";
        isValid = false;
    } else {
        passwordError.style.display = "none";
    }

    if (!isValid) {
        event.preventDefault(); // Pause PHP POST if JS validation fails
    }
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>
