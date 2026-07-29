<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['password'] == $password) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role']    = $row['role'];

            if ($row['role'] == "admin") {
                header("Location: admin/dashboard.php");
                exit();
            } else {
                header("Location: dashboard.php");
                exit();
            }

        } else {
            $message = "Incorrect password.";
            $message_type = "error";
        }

    } else {
        $message = "No account found with that email.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar h3 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
        }

        /* Main */
        .main {
            margin-left: 210px;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Login Card */
        .login-card {
            width: 380px;
            background: white;
            padding: 28px 30px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .login-card h3 {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background: #fafafa;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2c3e50;
            background: #fff;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 6px;
        }

        .btn-login:hover {
            background: #34495e;
        }

        .message {
            margin-top: 14px;
            padding: 10px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .message.error {
            background: #fdf0f0;
            color: #e74c3c;
            border: 1px solid #f5c6cb;
        }

        .register-link {
            margin-top: 14px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }

        .register-link a {
            color: #2c3e50;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Library</h3>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php" class="active">Login</a>
</div>

<!-- Main Content -->
<div class="main">
    <h2>Welcome Back</h2>

    <div class="login-card">
        <h3>Login to Your Account</h3>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>