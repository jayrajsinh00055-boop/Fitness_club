<?php
session_start();
include 'db.php';

// If already logged in, redirect straight to panel
if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    header("Location: admin_panel.php");
    exit;
}

// ── Hardcoded Admin Credentials ──────────────────────
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
// ─────────────────────────────────────────────────────

$error_message = '';

// Handle Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['adminLoggedIn'] = true;
        $_SESSION['adminUser']     = $username;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — EliteFit GYM</title>
    <link rel="stylesheet" href="style.css?v=7">
    <style>
        .admin-login-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .admin-login-card {
            width: 420px;
            max-width: 100%;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px 35px;
            text-align: center;
            animation: fadeInUp 0.5s ease;
        }

        .admin-login-card .login-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .admin-login-card h1 {
            font-family: 'Arial Black', sans-serif;
            font-size: 1.8rem;
            color: #333;
            margin: 0 0 5px 0;
        }

        .admin-login-card .subtitle {
            color: #888;
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .admin-login-card .form-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .admin-login-card .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-login-card .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Arial', sans-serif;
            background: #fff;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-login-card .form-group input:focus {
            outline: none;
            border-color: #888;
            box-shadow: 0 0 0 3px rgba(136, 136, 136, 0.15);
        }

        .admin-login-card .error-text {
            display: none;
            color: #c0392b;
            font-size: 0.82rem;
            font-weight: bold;
            margin-top: 5px;
        }

        .admin-login-card .server-error {
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #f5c6cb;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .admin-login-card .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.05rem;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            margin-top: 5px;
        }

        .admin-login-card .btn-login:hover {
            background-color: #555;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .admin-login-card .back-link {
            margin-top: 25px;
        }

        .admin-login-card .back-link a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .admin-login-card .back-link a:hover {
            color: #333;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="blog-content">
        <h1>ADMIN LOGIN</h1>
        <p class="blog-text">Gym Management Portal</p>
    </div>

    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="login-icon">⚙️</div>
            <h1>FITNESS CLUB</h1>
            <p class="subtitle">Admin Dashboard Access</p>

            <?php if (!empty($error_message)): ?>
            <div class="server-error">
                ⚠️ <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="admin_login.php" id="adminForm">
                <div class="form-group">
                    <label>Username</label>
                    <input name="username" id="username" type="text" placeholder="Enter admin username" autocomplete="username" autofocus>
                    <div id="userError" class="error-text"></div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input name="password" id="password" type="password" placeholder="Enter password" autocomplete="current-password">
                    <div id="passError" class="error-text"></div>
                </div>
                <button type="submit" class="btn-login">🔐 Login to Dashboard</button>
            </form>

            <div class="back-link">
                <a href="indexpage.php">← Back to Website</a>
            </div>
        </div>
    </div>

<script>
document.getElementById('adminForm').addEventListener('submit', function(event) {
    let isValid = true;

    const user = document.getElementById('username').value.trim();
    const userError = document.getElementById('userError');
    if (user === "") {
        userError.style.display = "block";
        userError.innerHTML = "Username is required";
        isValid = false;
    } else {
        userError.style.display = "none";
    }

    const password = document.getElementById('password').value;
    const passError = document.getElementById('passError');
    if (password === "") {
        passError.style.display = "block";
        passError.innerHTML = "Password is required";
        isValid = false;
    } else {
        passError.style.display = "none";
    }

    if (!isValid) { event.preventDefault(); }
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>
