<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== "user") {
    header("Location: admin/dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$userQuery = "SELECT name FROM users WHERE id = '$user_id'";
$userResult = mysqli_query($conn, $userQuery);
$userRow = mysqli_fetch_assoc($userResult);
$user_name = $userRow['name'];

$message = "";
$message_type = "";

if (isset($_POST['request_book'])) {
    $book_name   = mysqli_real_escape_string($conn, $_POST['book_name']);
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);

    if (!empty($book_name) && !empty($author_name)) {
        $sql = "INSERT INTO request 
                (user_id, name, book_name, author_name, request_date, status)
                VALUES 
                ('$user_id', '$user_name', '$book_name', '$author_name', NOW(), 'pending')";

        if (mysqli_query($conn, $sql)) {
            $message = "Book request submitted successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = "error";
        }
    } else {
        $message = "All fields are required.";
        $message_type = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Book</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        /* Sidebar */
        .sidebar {
            width: 220px; height: 100vh; background: #2c3e50;
            color: white; position: fixed; top: 0; left: 0;
            padding-top: 20px; z-index: 100;
        }
        .sidebar h3 {
            text-align: center; margin-bottom: 20px;
            font-size: 16px; padding: 0 10px; color: #ecf0f1;
        }
        .sidebar a {
            display: block; color: #bdc3c7;
            padding: 12px 20px; text-decoration: none;
            font-size: 14px; transition: background 0.2s, color 0.2s;
        }
        .sidebar a:hover,
        .sidebar a.active { background: #34495e; color: #fff; }

        /* Main */
        .main { margin-left: 220px; padding: 30px; }

        .page-title {
            font-size: 22px; font-weight: bold;
            color: #2c3e50; margin-bottom: 24px;
        }

        /* Form Card */
        .form-card {
            width: 400px;
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .form-card label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .form-card input {
            width: 100%;
            padding: 10px;
            margin-bottom: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-card input:focus {
            border-color: #2c3e50;
            outline: none;
        }

        .form-card button {
            width: 100%;
            padding: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .form-card button:hover {
            background: #34495e;
        }

        .message {
            margin-top: 14px;
            padding: 10px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .message.success { background: #eafaf1; color: #27ae60; border: 1px solid #a9dfbf; }
        .message.error   { background: #fdf0f0; color: #e74c3c; border: 1px solid #f5c6cb; }
        .message.warning { background: #fef9f0; color: #e67e22; border: 1px solid #f5cba7; }

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
    <h3>Dashboard</h3>
    <a href="dashboard.php">Home</a>
    <a href="history_user.php">History</a>
    <a href="request.php" class="active">Request</a>
    <a href="requestcheck.php">Check Request</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">
    <div class="page-title">Request a Book</div>

    <div class="form-card">
        <form method="POST">
            <label for="book_name">Book Name</label>
            <input type="text" id="book_name" name="book_name"
                   placeholder="e.g. The Great Gatsby" required>

            <label for="author_name">Author Name</label>
            <input type="text" id="author_name" name="author_name"
                   placeholder="e.g. F. Scott Fitzgerald" required>

            <button type="submit" name="request_book">Submit Request</button>
        </form>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

    <footer><p>NAJRUL LIBRARY</p></footer>
</div>

</body>
</html>
