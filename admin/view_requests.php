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

if (isset($_POST['mark_arrived'])) {
    $request_id = $_POST['request_id'];
    $update     = "UPDATE request SET status='arrived' WHERE id='$request_id'";
    mysqli_query($conn, $update);
}

$sql    = "SELECT * FROM request";
$result = mysqli_query($conn, $sql);
if (!$result) die("Database Error: " . mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests</title>
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

        .status-pending {
            padding: 4px 10px; border-radius: 20px; font-size: 12px;
            font-weight: bold; background: #fef9f0;
            color: #e67e22; border: 1px solid #e67e22;
        }
        .status-arrived {
            padding: 4px 10px; border-radius: 20px; font-size: 12px;
            font-weight: bold; background: #eafaf1;
            color: #27ae60; border: 1px solid #27ae60;
        }

        .btn-action {
            padding: 6px 14px; background: #2c3e50;
            color: white; border: none; border-radius: 4px;
            font-size: 12px; cursor: pointer;
        }
        .btn-action:hover { background: #34495e; }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="transaction.php">Transactions</a>
    <a href="view_requests.php" class="active">Requests</a>
    <a href="manage_users.php">Users</a>
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <div class="page-title">Book Requests</div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Book Name</th>
                <th>Author Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['book_name']) ?></td>
                <td><?= htmlspecialchars($row['author_name']) ?></td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <span class="status-pending">⏳ Pending</span>
                    <?php else: ?>
                        <span class="status-arrived">✅ Arrived</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="mark_arrived" class="btn-action">
                                Mark as Arrived
                            </button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>