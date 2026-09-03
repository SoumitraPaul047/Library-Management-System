Login · PHP
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
            $message = "Incorrect password!";
            $message_type = "error";
        }
 
    } else {
        $message = "No account found with that email.Try again!";
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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
 
        /* Sidebar */
        .sidebar {
            width: 230px; height: 110vh; background: #2c3e50;
            color: white; position: fixed; top: 0; left: 0;
            padding-top: 19px; z-index: 100;
        }
        .sidebar h3 {
            text-align: center; margin-bottom: 20px;
            font-size: 16px; padding: 0 10px; color: #ecf0f1;
        }
        .sidebar a {
            display: block; color: #bdc3c7;
            padding: 12px 19px; text-decoration: none;
            font-size: 14px; transition: background 0.2s, color 0.2s;
        }
        .sidebar a:hover,
        .sidebar a.active { background: #34495e; color: #fff; }
 
        /* Main */
        .main { margin-left: 220px; padding: 30px; }
 
        .page-title {
            font-size: 21px; font-weight: bold;
            color: #2c3e50; margin-bottom: 24px;
        }
 
        /* Login Card */
        .login-card {
            width: 378px;
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
            margin-top: 15px;
            font-size: 14px;
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
    <div class="page-title">Welcome Again!</div>
 
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
 
    <footer><p>NAJRUL LIBRARY</p></footer>
</div>
</body>
</html>
 
