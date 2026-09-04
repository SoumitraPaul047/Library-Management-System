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
        $search_condition = "AND b.author LIKE '%$search%'";
    } else {
        $search_condition = "AND b.title LIKE '%$search%'";
    }
} else {
    $search_type = (isset($_GET['search_type']) && $_GET['search_type'] === "author")
                   ? "author"
                   : "title";
    $search_condition = "";
}

// Fetch transaction histories with book info using JOIN
$sql = "SELECT t.*, b.title AS book_name, b.author AS book_author, b.image AS book_image
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        WHERE t.user_id = '$user_id'
        $search_condition
        ORDER BY t.issue_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User History</title>

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

        /* Main */
        .main { margin-left: 220px; padding: 30px; }

        .page-title {
            font-size: 22px; font-weight: bold;
            color: #2c3e50; margin-bottom: 24px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        thead {
            background: #2c3e50;
            color: white;
        }

        thead th {
            padding: 14px 18px;
            text-align: center;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f9f9f9;
        }

        tbody td {
            padding: 12px 18px;
            font-size: 14px;
            color: #333;
            text-align: center;
        }

        tbody img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status-borrowed {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            background: #fef9f0;
            color: #e67e22;
            border: 1px solid #e67e22;
        }

        .status-returned {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            background: #eafaf1;
            color: #27ae60;
            border: 1px solid #27ae60;
        }

        .no-history {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #aaa;
            font-size: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
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
    <h3>Dashboard</h3>

    <!-- Search Box -->
    <div class="search-box">
        <form method="GET" action="history_user.php">
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
                placeholder="Search history..."
                value="<?= htmlspecialchars($search) ?>"
            >
            <button type="submit">Search</button>
        </form>
    </div>

    <a href="dashboard.php">Home</a>
    <a href="history_user.php" class="active">History</a>
    <a href="request.php">Request</a>
    <a href="requestcheck.php">Check Request</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Contents -->
<div class="main">
    <div class="page-title">Your Transaction History</div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Book Image</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['book_id']) ?></td>
                    <td><?= htmlspecialchars($row['book_name']) ?></td>
                    <td><?= htmlspecialchars($row['book_author']) ?></td>
                    <td>
                        <img src="image/<?= htmlspecialchars($row['book_image']) ?>" alt="Book">
                    </td>
                    <td><?= htmlspecialchars($row['issue_date']) ?></td>
                    <td>
                        <?= $row['return_date'] ? htmlspecialchars($row['return_date']) : "Not Returned" ?>
                    </td>
                    <td>
                        <?php if (strtolower($row['status']) === 'returned'): ?>
                            <span class="status-returned">✅ Returned</span>
                        <?php else: ?>
                            <span class="status-borrowed">📖 Borrowed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-history">
            No transaction history found<?= $search ? " for \"" . htmlspecialchars($search) . "\"" : "" ?>.
        </div>
    <?php endif; ?>

    <footer><p>NAJRUL LIBRARY</p></footer>
</div>

</body>
</html>
