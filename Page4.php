<?php
session_start();
include 'db.php';

// Auth Guard: Prevent logged-in users from accessing Registration
if(isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true){
    echo "<script>window.location.href='admin_panel.php';</script>";
    exit;
}
if(isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true){
    echo "<script>window.location.href='homepage.php';</script>";
    exit;
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $age = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $password = $_POST['password'];
    
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($age) && !empty($gender) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO members (name, email, password, phone, age, gender) VALUES ('$name', '$email', '$hashed_password', '$phone', $age, '$gender')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Member successfully registered in Database!'); window.location.href = 'Page7.php';</script>";
            exit;
        } else {
            if ($conn->errno == 1062) {
                echo "<script>alert('Error: Email is already registered!');</script>";
            } else {
                echo "<script>alert('Database Error: " . $conn->error . "');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="style.css?v=7">

    <style>
        body { font: 16px Arial, sans-serif; }
        b.error { color: red; font-size: 14px; display: none; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="blog-content">
        <h1>REGISTRATION</h1>
        <p class="blog-text">Best experience for joining our gym</p>
    </div>

    <center>
        <div class="registration-form"><br>
            <form method="POST" action="Page4.php" id="registerForm">
                <h1>Registration Form</h1><br>

                <input class="input" name="name" id="name" type="text" placeholder="Enter your name" ><br>
                <b id="nameError" class="error"></b><br>

                <input class="input" name="email" id="email" type="email" placeholder="Enter your Email" ><br>
                <b id="emailError" class="error"></b><br>

                <input class="input" name="password" id="password" type="password" placeholder="Create a Password" ><br>
                <b id="passwordError" class="error"></b><br>

                <input class="input" type="number" name="phone" id="phone" placeholder="Enter your Number" ><br>
                <b id="phoneError" class="error"></b><br>

                <input class="input" type="number" name="age" id="age" placeholder="Enter your Age" ><br>
                <b id="ageError" class="error"></b><br>

                Gender<br>
                <div class="gender-group">
                    <label><input type="radio" name="gender" value="Male" > Male</label>
                    <label><input type="radio" name="gender" value="Female" > Female</label>
                </div>
                <b id="genderError" class="error"></b><br>

                <button type="submit" class="reg">Register</button><br><br>
            </form>
        </div><br><br>
    </center>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Name Validation
    const name = document.getElementById('name').value.trim();
    const name_regex = /^[A-Za-z\s]+$/;
    const nameError = document.getElementById('nameError');
    if (name === "") {
        nameError.style.display = "block"; nameError.innerHTML = "Name is required"; isValid = false;
    } else if (name.length < 2) {
        nameError.style.display = "block"; nameError.innerHTML = "Name must be at least 2 characters."; isValid = false;
    } else if (!name_regex.test(name)) {
        nameError.style.display = "block"; nameError.innerHTML = "Name can contain only letters."; isValid = false;
    } else {
        nameError.style.display = "none";
    }

    // Email Validation
    const email = document.getElementById('email').value.trim();
    const email_regex = /^[a-z0-9]+@[a-z0-9]+\.[a-z]{2,}$/;
    const emailError = document.getElementById('emailError');
    if (email === "") {
        emailError.style.display = "block"; emailError.innerHTML = "Email is required"; isValid = false;
    } else if (!email_regex.test(email)) {
        emailError.style.display = "block"; emailError.innerHTML = "Invalid email format"; isValid = false;
    } else {
        emailError.style.display = "none";
    }

    // Password Validation
    const pwd = document.getElementById('password').value;
    const pwdError = document.getElementById('passwordError');
    if (pwd === "") {
        pwdError.style.display = "block"; pwdError.innerHTML = "Password is required"; isValid = false;
    } else if (pwd.length < 6) {
        pwdError.style.display = "block"; pwdError.innerHTML = "Password must be at least 6 characters"; isValid = false;
    } else {
        pwdError.style.display = "none";
    }

    // Phone Validation
    const phone = document.getElementById('phone').value;
    const phoneError = document.getElementById('phoneError');
    if (phone === "") {
        phoneError.style.display = "block"; phoneError.innerHTML = "Number is required"; isValid = false;
    } else if (phone.length !== 10) {
        phoneError.style.display = "block"; phoneError.innerHTML = "Number must be exactly 10 digits"; isValid = false;
    } else {
        phoneError.style.display = "none";
    }

    // Age Validation
    const age = document.getElementById('age').value;
    const ageError = document.getElementById('ageError');
    if (age === "") {
        ageError.style.display = "block"; ageError.innerHTML = "Age is required"; isValid = false;
    } else if (age < 10 || age > 100) {
        ageError.style.display = "block"; ageError.innerHTML = "Age must be between 10 and 100"; isValid = false;
    } else {
        ageError.style.display = "none";
    }

    // Gender Validation
    const gender = document.querySelector('input[name="gender"]:checked');
    const genderError = document.getElementById('genderError');
    if (!gender) {
        genderError.style.display = "block"; genderError.innerHTML = "Please select your gender"; isValid = false;
    } else {
        genderError.style.display = "none";
    }

    if (!isValid) {
        event.preventDefault(); // Stop form submission to PHP
    }
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>
