<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

include "../db.php";

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $address  = mysqli_real_escape_string($conn, trim($_POST['address']));
    $mobile   = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $role     = ($_POST['role'] === "admin") ? "admin" : "user";

    if ($name === "" || $email === "" || $password === "" || $address === "" || $mobile === "") {
        $message      = "All fields are required.";
        $message_type = "error";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");

        if ($check && mysqli_num_rows($check) > 0) {
            $message      = "This email is already registered.";
            $message_type = "error";
        } else {
            $sql = "INSERT INTO users (name, email, password, address, mobile, role)
                    VALUES ('$name', '$email', '$password', '$address', '$mobile', '$role')";

            $result = mysqli_query($conn, $sql);

            if (!$result) {
                $message      = "Error: " . mysqli_error($conn);
                $message_type = "error";
            } else {
                $message      = "User added successfully!";
                $message_type = "success";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Users</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            top: 0; left: 0;
            padding-top: 20px;
            z-index: 100;
        }
        .sidebar h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            padding: 0 10px;
            color: #ecf0f1;
        }
        .sidebar a {
            display: block;
            color: #bdc3c7;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar a:hover,
        .sidebar a.active { background: #34495e; color: #fff; }

        /* ── Main ── */
        .main { margin-left: 220px; padding: 30px; }

        .page-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 24px;
        }

        /* ── Form Card ── */
        .form-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            padding: 30px;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="tel"],
        .form-group select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 13px;
            color: #333;
            outline: none;
            transition: border 0.2s;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2c3e50;
        }

        .btn-submit {
            width: 100%;
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 11px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #34495e; }

        /* ── Messages ── */
        .msg {
            padding: 10px 16px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 20px;
            max-width: 500px;
        }
        .msg.success { background: #eafaf1; color: #27ae60; border: 1px solid #a9dfbf; }
        .msg.error   { background: #fdf2f2; color: #c0392b; border: 1px solid #f5b7b1; }

        footer {
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
            color: #888;
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="transaction.php">Transactions</a>
    <a href="view_requests.php">Requests</a>
    <a href="manage_users.php">Users</a>
    <a href="add_users.php" class="active">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="page-title">Add New User</div>

    <?php if ($message): ?>
        <div class="msg <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form action="" method="post">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter email address" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Enter address" required>
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="tel" name="mobile" placeholder="Enter mobile number" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Add User</button>
        </form>
    </div>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>