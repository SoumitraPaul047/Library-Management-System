<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Transactions</title>
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
            padding: 14px 18px; text-align: center;
            font-size: 14px;
        }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:hover { background: #f9f9f9; }
        tbody td {
            padding: 12px 18px; font-size: 14px;
            color: #333; text-align: center;
        }

        input[type="date"] {
            padding: 5px 8px; font-size: 13px;
            border: 1px solid #ddd; border-radius: 4px;
        }

        .btn-action {
            padding: 6px 14px; background: #2c3e50;
            color: white; border: none; border-radius: 4px;
            font-size: 12px; cursor: pointer;
        }
        .btn-action:hover { background: #34495e; }

        .status-returned {
            padding: 4px 10px; border-radius: 20px;
            font-size: 12px; font-weight: bold;
            background: #eafaf1; color: #27ae60;
            border: 1px solid #27ae60;
        }

        footer { margin-top: 30px; text-align: center; color: #888; font-size: 13px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function markReturned(transaction_id, btn) {
        $.ajax({
            url: 'mark_returned.php',
            method: 'POST',
            data: { transaction_id: transaction_id },
            success: function(response) {
                if (response == "success") {
                    $(btn).replaceWith('<span class="status-returned">✅ Returned</span>');
                } else {
                    alert("Error updating transaction.");
                }
            }
        });
    }
    </script>
</head>
<body>
<div class="sidebar">
    <h3>Admin Dashboard</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="transaction.php" class="active">Transactions</a>
    <a href="view_requests.php">Requests</a>
    <a href="manage_users.php">Users</a>
    <a href="add_users.php">Add Users</a>
    <a href="add_book.php">Add Books</a>
    <a href="history_user.php">History</a>
    <a href="../logout.php">Logout</a>
</div>
<div class="main">
    <div class="page-title">All Transactions</div>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>User Name</th>
                <th>Book ID</th>
                <th>Book Name</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT t.id as transaction_id, t.user_id, u.name as user_name,
                       t.book_id, b.title as book_name, t.issue_date, t.return_date, t.status
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                JOIN books b ON t.book_id = b.id
                ORDER BY t.issue_date DESC";

        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
                $return_val = $row['return_date']
                    ? date('Y-m-d', strtotime($row['return_date'])) : '';
        ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= $row['book_id'] ?></td>
                <td><?= htmlspecialchars($row['book_name']) ?></td>
                <td><?= $row['issue_date'] ?></td>
                <td><input type="date" value="<?= $return_val ?>"></td>
                <td>
                    <?php if ($row['status'] == "borrowed"): ?>
                        <button class="btn-action"
                            onclick="markReturned(<?= $row['transaction_id'] ?>, this)">
                            Borrowed
                        </button>
                    <?php else: ?>
                        <span class="status-returned">✅ Returned</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="7">No transactions found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <footer><p>NAJRUL LIBRARY</p></footer>
</div>
</body>
</html>
