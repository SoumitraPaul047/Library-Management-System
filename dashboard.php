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

// Fetch books from data base
$sql = "SELECT * FROM books";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <style>
        body {
            font-family: Arial;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #34495e;
        }

        /* Main Content */
        .main {
            margin-left: 210px;
            padding: 20px;
        }

        /* Book Section */
        .indexsection {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .indexsection div {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .indexsection img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 style="text-align:center;">Dashboard</h3>
    <a href="history_user.php">History</a>
    <a href="request.php">Request</a>           <!-- ✅ ADDED -->
    <a href="requestcheck.php">Check Request</a> <!-- ✅ ADDED -->
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <h2>Available Books</h2>

  <section class="indexsection">
<?php if ($result && mysqli_num_rows($result) > 0) { ?>

    <?php 
    $user_id = $_SESSION['user_id'];

    while ($row = mysqli_fetch_assoc($result)) { 

        $book_id = $row['id'];

        // Check if already borrowed
        $check_sql = "SELECT * FROM transactions 
                      WHERE user_id = '$user_id' 
                      AND book_id = '$book_id' 
                      AND status = 'Borrowed'";
        $check_result = mysqli_query($conn, $check_sql);
        $isBorrowed = mysqli_num_rows($check_result) > 0;
    ?>
    
    <div>
        <img src="image/<?php echo $row['image']; ?>" alt="Book Image">

        <!-- BUTTON / STATUS -->
        <?php if ($isBorrowed) { ?>
            <p style="color: green;"><b>Borrowed</b></p>

        <?php } else { ?>

            <?php if ($row['quantity'] > 0) { ?>
                <form action="borrow.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <button type="submit">Borrow</button>
                </form>

            <?php } else { ?>
                <p style="color:red;"><b>Out of Stock</b></p>

                <form action="request.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <button type="submit">Request</button>
                </form>
            <?php } ?>

        <?php } ?>

        <h2><?php echo $row['title']; ?></h2>
        <h3>Author: <?php echo $row['author']; ?></h3>
        <p>ISBN: <?php echo $row['isbn']; ?></p>
        <p>Available: <?php echo $row['quantity']; ?></p>
    </div>

    <?php } ?>

<?php } else { ?>
    <p>No books available.</p>
<?php } ?>
</section>

    <footer>
        <p>Najrul LIBRARY</p>
    </footer>
</div>

</body>
</html>
