<?php
session_start();
include "db.php";

/* SEARCH LOGIC  */
/* SEARCH LOGIC */
$search      = "";
$search_type = "title"; // default
if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    // Get search type, default to title
    $search_type = (isset($_GET['search_type']) && $_GET['search_type'] === "author") 
                   ? "author" 
                   : "title";

    if ($search_type === "author") {
        $sql = "SELECT * FROM books 
                WHERE quantity > 0 
                AND author LIKE '%$search%'";
    } else {
        $sql = "SELECT * FROM books 
                WHERE quantity > 0 
                AND title LIKE '%$search%'";
    }
} 
else {
    // No search — also preserve search_type if set
    $search_type = (isset($_GET['search_type']) && $_GET['search_type'] === "author")
                   ? "author"
                   : "title";
    $sql = "SELECT * FROM books WHERE quantity > 0";
}
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error!: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            margin: 0;
            background: #f4f4f4;
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

        .sidebar h3 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
        }

        /* Search inside sidebar */
        .sidebar .search-box {
            padding: 10px 12px;
            border-top: 1px solid #3d5166;
            border-bottom: 1px solid #3d5166;
        }

        /* Toggle buttons for search type */
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
            font-size: 14px;
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
            font-size: 14px;
            cursor: pointer;
        }

        .sidebar .search-box button:hover {
            background: #4a6278;
        }

        /* Main */
        .main {
            margin-left: 210px;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Book Grid */
        .indexsection {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #fff;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .book-card:hover {
            transform: translateY(-3px);
        }

        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book-card h2 {
            font-size: 14px;
            color: #2c3e50;
            margin: 6px 0 4px;
        }

        .book-card h3 {
            font-size: 12px;
            color: #666;
            font-weight: normal;
            margin-bottom: 4px;
        }

        .book-card p {
            font-size: 12px;
            color: #888;
            margin: 2px 0;
        }

        .no-books {
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
    <h3>Library</h3>

   <!-- Search Box -->
<div class="search-box">
    <form method="GET" action="index.php">

        <!-- Toggle: Book Name / Author -->
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

    <!-- Nav Links -->
    <a href="index.php" class="active">Home</a>
    <a href="register.php">Register</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

<!-- Main Contents -->
<div class="main">
    <h2>
        <?php
        if ($search) {
            $type_label = ($search_type === 'author') ? 'Author' : 'Book Name';
            echo "Search Results for \"" . htmlspecialchars($search) . "\" by " . $type_label;
        } else {
            echo "Available Books";
        }
        ?>
    </h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="indexsection">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="book-card">
                    <img src="image/<?= htmlspecialchars($row['image']) ?>" alt="Book Image">
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
