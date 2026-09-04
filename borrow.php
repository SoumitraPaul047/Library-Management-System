<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

// Insert transactions
$sql = "INSERT INTO transactions (user_id, book_id, issue_date, status)
        VALUES ('$user_id', '$book_id', NOW(), 'Borrowed')";
mysqli_query($conn, $sql);

// Decrease quantity
mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id = '$book_id'");

header("Location: dashboard.php");
exit();
