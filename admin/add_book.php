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
    $title    = $_POST['title'];
    $author   = $_POST['author'];
    $isbn     = $_POST['isbn'];
    $image    = $_FILES['image']['name'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO books(title, author, isbn, image, quantity)
            VALUES('$title','$author','$isbn','$image','$quantity')";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $message      = "Error: " . mysqli_error($conn);
        $message_type = "error";
    } else {
        $image_location  = $_FILES['image']['tmp_name'];
        $upload_location = "../image/";
        move_uploaded_file($image_location, $upload_location . $image);
        $message      = "Book added successfully!";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
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
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 13px;
            color: #333;
            outline: none;
            transition: border 0.2s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus {
            border-color: #2c3e50;
        }

        .form-group input[type="file"] {
            padding: 6px;
            cursor: pointer;
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
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php" class="active">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="page-title">Add New Book</div>

    <?php if ($message): ?>
        <div class="msg <?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Enter book title" required>
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" placeholder="Enter author name" required>
            </div>

            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" placeholder="Enter ISBN" required>
            </div>

            <div class="form-group">
                <label>Book Cover Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" placeholder="Enter quantity" min="1" required>
            </div>

            <button type="submit" class="btn-submit">Add Book</button>
        </form>
    </div>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>