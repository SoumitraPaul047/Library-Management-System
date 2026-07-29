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

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql       = "DELETE FROM users WHERE id = '$delete_id'";
    $result    = mysqli_query($conn, $sql);
    if (!$result) die("Delete Error: " . mysqli_error($conn));
    header("Location: manage_users.php");
    exit();
}

$sql    = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
if (!$result) die("Fetch Error: " . mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        .btn-delete {
            padding: 6px 14px; background: #e74c3c;
            color: white; border: none; border-radius: 4px;
            font-size: 12px; cursor: pointer; text-decoration: none;
        }
        .btn-delete:hover { background: #c0392b; }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="transaction.php">Transactions</a>
    <a href="view_requests.php">Requests</a>
    <a href="manage_users.php" class="active">Users</a>
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <div class="page-title">Manage Users</div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Mobile</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['mobile']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <a class="btn-delete"
                       href="manage_users.php?delete_id=<?= $row['id'] ?>"
                       onclick="return confirm('Delete this user?')">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <footer><p>Najrul LIBRARY</p></footer>
</div>

</body>
</html>