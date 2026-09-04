<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id'])){
    header("Location:../login.php");
    exit;
}

if($_SESSION['role'] != "admin"){
    header("Location:../dashboard.php");
    exit;
}

if(!isset($_GET['transaction_id'])){
    header("Location:view_transactions.php");
    exit;
}

$transaction_id = $_GET['transaction_id'];

$sql = "DELETE FROM transactions WHERE id = '$transaction_id'";
$result = mysqli_query($conn, $sql);

if(!$result){
    echo "Error!: " . mysqli_error($conn);
} else {
    header("Location: view_transactions.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="update_transactions.php?transaction_id=<?php echo $transaction_id; ?>" method="post">
        <input type="text" name="returndate" required placeholder="date-format:2025-03-04">
        <input type="submit" name="submit" value="update">
    </form>
</body>
</html>