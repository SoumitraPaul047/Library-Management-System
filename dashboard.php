<?php
session_start();
include "db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect admin
if ($_SESSION['role'] == "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* SEARCH LOGIC */
$search      = "";
$search_type = "title"; // default

if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search      = mysqli_real_escape_string($conn, trim($_GET['search']));
    $search_type = (isset($_GET['search_type']) && $_GET['search_type'] === "author")
                   ? "author"
                   : "title";

    if ($search_type === "author") {
        $sql = "SELECT * FROM books WHERE author LIKE '%$search%' ORDER BY title ASC";
    } else {
        $sql = "SELECT * FROM books WHERE title LIKE '%$search%' ORDER BY title ASC";
    }
} else {
    $search_type = (isset($_GET['search_type']) && $_GET['search_type'] === "author")
                   ? "author"
                   : "title";
    $sql = "SELECT * FROM books ORDER BY title ASC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

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

        /* Search inside sidebar */
        .sidebar .search-box {
            padding: 10px 12px;
            border-top: 1px solid #3d5166;
            border-bottom: 1px solid #3d5166;
        }

        .search-toggle {
            display: flex;
            margin-bottom: 8px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #4a6278;
        }

        .search-toggle label {
            flex: 1;
            text-align: center;
            padding: 6px 4px;
            font-size: 12px;
            color: #bdc3c7;
            background: #3d5166;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .search-toggle input[type="radio"] {
            display: none;
        }

        .search-toggle input[type="radio"]:checked + label {
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
            border-bottom: 2px solid #3498db;
        }

        .sidebar .search-box input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            background: #3d5166;
            color: white;
            outline: none;
        }

        .sidebar .search-box input[type="text"]::placeholder {
            color: #95a5a6;
        }

        .sidebar .search-box button {
            width: 100%;
            margin-top: 7px;
            padding: 8px;
            background: #34495e;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        .sidebar .search-box button:hover {
            background: #4a6278;
        }

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

        .no-books {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #aaa;
            font-size: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Dashboard</h3>

    <!-- Search Box -->
    <div class="search-box">
        <form method="GET" action="dashboard.php">
            <div class="search-toggle">
                <input type="radio" name="search_type" id="type_title" value="title"
                    <?= ($search_type !== 'author') ? 'checked' : '' ?>>
                <label for="type_title">Book Name</label>

                <input type="radio" name="search_type" id="type_author" value="author"
                    <?= ($search_type === 'author') ? 'checked' : '' ?>>
                <label for="type_author">Author</label>
            </div>

            <input
                type="text"
                name="search"
                placeholder="Search books..."
                value="<?= htmlspecialchars($search) ?>"
            >
            <button type="submit">Search</button>
        </form>
    </div>

    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="history_user.php">History</a>
    <a href="request.php">Request</a>
    <a href="requestcheck.php">Check Request</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Contents -->
<div class="main">
    <div class="page-title">
        <?php
        if ($search) {
            $type_label = ($search_type === 'author') ? 'Author' : 'Book Name';
            echo "Search Results for \"" . htmlspecialchars($search) . "\" by " . $type_label;
        } else {
            echo "Available Books";
        }
        ?>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="indexsection">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $book_id = $row['id'];

                // Check if already borrowed
                $check_sql = "SELECT * FROM transactions 
                              WHERE user_id = '$user_id' 
                              AND book_id = '$book_id' 
                              AND status = 'Borrowed'";
                $check_result = mysqli_query($conn, $check_sql);
                $isBorrowed = mysqli_num_rows($check_result) > 0;
            ?>
            <div class="book-card">
                <img src="image/<?= htmlspecialchars($row['image']) ?>" alt="Book Image">

                <?php if ($isBorrowed): ?>
                    <p class="borrowed-label">✅ Borrowed</p>
                <?php elseif ($row['quantity'] > 0): ?>
                    <form action="borrow.php" method="post">
                        <input type="hidden" name="book_id" value="<?= $book_id ?>">
                        <button type="submit">Borrow</button>
                    </form>
                <?php else: ?>
                    <p class="out-of-stock">❌ Out of Stock</p>
                    <form action="request.php" method="post">
                        <input type="hidden" name="book_id" value="<?= $book_id ?>">
                        <button type="submit">Request</button>
                    </form>
                <?php endif; ?>

                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <h3>Author: <?= htmlspecialchars($row['author']) ?></h3>
                <p>ISBN: <?= htmlspecialchars($row['isbn']) ?></p>
                <p>Available: <?= $row['quantity'] ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-books">
            No books found<?= $search ? " for \"" . htmlspecialchars($search) . "\"" : "" ?>.
        </div>
    <?php endif; ?>

    <footer><p>NAJRUL LIBRARY</p></footer>
</div>

</body>
</html>
