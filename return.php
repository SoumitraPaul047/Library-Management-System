<?php
include "db.php";

$transaction_id = $_GET['id'];

// Update transaction
mysqli_query($conn, "
    UPDATE transactions 
    SET status='Returned', return_date=NOW() 
    WHERE id='$transaction_id'
");

// Increase quantity
$book = mysqli_query($conn, "
    SELECT book_id FROM transactions WHERE id='$transaction_id'
");
$row = mysqli_fetch_assoc($book);

mysqli_query($conn, "
    UPDATE books SET quantity = quantity + 1 
    WHERE id='{$row['book_id']}'
");

header("Location: history_user.php");