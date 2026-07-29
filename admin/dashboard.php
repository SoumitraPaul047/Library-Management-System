<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$book_sql    = "SELECT * FROM books ORDER BY title ASC";
$book_result = mysqli_query($conn, $book_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

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

        .main { margin-left: 220px; padding: 30px; }

        .page-title {
            font-size: 22px; font-weight: bold;
            color: #2c3e50; margin-bottom: 24px;
        }

        .indexsection {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #fff; border-radius: 8px;
            padding: 12px; text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .book-card:hover { transform: translateY(-3px); }

        .book-card img {
            width: 100%; height: 180px;
            object-fit: cover; border-radius: 5px;
            margin-bottom: 10px;
        }
        .book-card h2 { font-size: 13px; color: #2c3e50; margin: 4px 0; }
        .book-card h3 { font-size: 12px; color: #666; font-weight: normal; }
        .book-card p  { font-size: 12px; color: #888; margin: 2px 0; }

        .book-card button {
            padding: 6px 14px; margin-top: 6px;
            background: #2c3e50; color: white;
            border: none; border-radius: 4px;
            font-size: 12px; cursor: pointer;
        }
        .book-card button:hover { background: #34495e; }

        .borrowed-label { color: #27ae60; font-weight: bold; font-size: 13px; }
        .out-of-stock   { color: #e74c3c; font-weight: bold; font-size: 13px; }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="transaction.php">Transactions</a>
    <a href="view_requests.php">Requests</a>
    <a href="manage_users.php">Users</a>
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <div class="page-title">Available Books</div>

    <div class="indexsection">
        <?php if ($book_result && mysqli_num_rows($book_result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($book_result)):
                $book_id = $row['id'];
                $check_sql    = "SELECT * FROM transactions 
                                 WHERE user_id = '$user_id' 
                                 AND book_id = '$book_id' 
                                 AND status = 'Borrowed'";
                $check_result = mysqli_query($conn, $check_sql);
                $isBorrowed   = mysqli_num_rows($check_result) > 0;
            ?>
            <div class="book-card">
                <img src="../image/<?= htmlspecialchars($row['image']) ?>" alt="Book Image">

                <?php if ($isBorrowed): ?>
                    <p class="borrowed-label">✅ Borrowed</p>
                <?php elseif ($row['quantity'] > 0): ?>
                    <form action="borrow.php" method="post">
                        <input type="hidden" name="book_id" value="<?= $book_id ?>">
                        <button type="submit">Borrow</button>
                    </form>
                <?php else: ?>
                    <p class="out-of-stock">❌ Out of Stock</p>
                <?php endif; ?>

                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <h3>Author: <?= htmlspecialchars($row['author']) ?></h3>
                <p>ISBN: <?= htmlspecialchars($row['isbn']) ?></p>
                <p>Available: <?= $row['quantity'] ?></p>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No books available.</p>
        <?php endif; ?>
    </div>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>