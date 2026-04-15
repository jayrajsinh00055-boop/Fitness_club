<?php
include 'db.php';
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $subject = "Website Contact Form";
        $sql = "INSERT INTO messages (sender, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Message sent successfully! We will get back to you soon.');</script>";
        } else {
            echo "<script>alert('Database Error: " . $conn->error . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css?v=7">

    <style>
        body { font: 16px Arial, sans-serif; }
        b.error { color: red; font-size: 14px; display: none; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="blog-content">
        <h1>CONTACT US</h1>
        <p class="blog-text">Get in touch with EliteFit GYM</p>
    </div>

    <div class="contact-container">
        <h2>Send us a Message</h2>
        <form class="contact-form" method="POST" action="Page6.php" id="contactForm">

            <input type="text" name="name" id="contactName" placeholder="Your Name" >
            <b id="contactNameError" class="error"></b>

            <input type="email" name="email" id="contactEmail" placeholder="Your Email" >
            <b id="contactEmailError" class="error"></b>

            <textarea rows="5" name="message" id="contactMessage" placeholder="Your Message" ></textarea>
            <b id="contactMessageError" class="error"></b>
            
            <button type="submit">Send Message</button>
        </form>
    </div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Name Validation
    const name = document.getElementById('contactName').value.trim();
    const name_regex = /^[A-Za-z\s]+$/;
    const nameError = document.getElementById('contactNameError');
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
    const email = document.getElementById('contactEmail').value.trim();
    const email_regex = /^[a-z0-9]+@[a-z0-9]+\.[a-z]{2,}$/;
    const emailError = document.getElementById('contactEmailError');
    if (email === "") {
        emailError.style.display = "block"; emailError.innerHTML = "Email is required"; isValid = false;
    } else if (!email_regex.test(email)) {
        emailError.style.display = "block"; emailError.innerHTML = "Invalid email format"; isValid = false;
    } else {
        emailError.style.display = "none";
    }

    // Message Validation
    const message = document.getElementById('contactMessage').value.trim();
    const messageError = document.getElementById('contactMessageError');
    if (message === "") {
        messageError.style.display = "block"; messageError.innerHTML = "Message is required"; isValid = false;
    } else if (message.length < 10) {
        messageError.style.display = "block"; messageError.innerHTML = "Message must be at least 10 characters long"; isValid = false;
    } else {
        messageError.style.display = "none";
    }

    if (!isValid) {
        event.preventDefault(); // Stop form submission to PHP
    }
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>
