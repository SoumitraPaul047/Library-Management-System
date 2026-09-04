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

$sql = "SELECT t.*, b.title AS book_name, b.image AS book_image, u.name AS user_name
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        JOIN users u ON t.user_id = u.id
        ORDER BY t.issue_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
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

        table {
            width: 100%; border-collapse: collapse;
            background: #fff; border-radius: 8px;
            overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        thead { background: #2c3e50; color: white; }
        thead th {
            padding: 14px 18px; text-align: center; font-size: 14px;
        }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:hover { background: #f9f9f9; }
        tbody td {
            padding: 12px 18px; font-size: 14px;
            color: #333; text-align: center;
        }
        tbody img {
            width: 55px; height: 75px;
            object-fit: cover; border-radius: 4px;
        }

        .status-borrowed {
            padding: 4px 10px; border-radius: 20px; font-size: 12px;
            font-weight: bold; background: #fef9f0;
            color: #e67e22; border: 1px solid #e67e22;
        }
        .status-returned {
            padding: 4px 10px; border-radius: 20px; font-size: 12px;
            font-weight: bold; background: #eafaf1;
            color: #27ae60; border: 1px solid #27ae60;
        }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="transaction.php">Transactions</a>
    <a href="view_requests.php">Requests</a>
    <a href="manage_users.php">Users</a>
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php" class="active">History</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <div class="page-title">Transaction History</div>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Book ID</th>
                <th>Book Name</th>
                <th>Book Image</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['book_id']) ?></td>
                <td><?= htmlspecialchars($row['book_name']) ?></td>
                <td>
                    <img src="../image/<?= htmlspecialchars($row['book_image']) ?>" alt="Book">
                </td>
                <td><?= htmlspecialchars($row['issue_date']) ?></td>
                <td><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : "Not Returned" ?></td>
                <td>
                    <?php if (strtolower($row['status']) === 'returned'): ?>
                        <span class="status-returned">✅ Returned</span>
                    <?php else: ?>
                        <span class="status-borrowed">📖 Borrowed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No history found!</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <footer><p>NAJRUL LIBRARY</p></footer>
</div>

</body>
</html>
