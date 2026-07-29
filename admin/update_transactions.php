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

if(isset($_POST['submit'])){
    $returndate = $_POST['returndate'];
    $status     = $_POST['status'];

    $sql = "UPDATE transactions 
            SET return_date = '$returndate',
                status = '$status'
            WHERE id = '$transaction_id'";

    $result = mysqli_query($conn,$sql);

    if(!$result){
        echo "Error!: " . mysqli_error($conn);
    } else {
        header("Location: view_transactions.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Transaction</title>
</head>
<body>

<form action="update_transactions.php?transaction_id=<?php echo $transaction_id; ?>" method="post">

    <label>Return Date:</label><br>
    <input type="date" name="returndate" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="">-- Select Status --</option>
        <option value="borrowed">Borrowed</option>
        <option value="returned">Returned</option>
    </select><br><br>

    <input type="submit" name="submit" value="Update">
</form>

</body>
</html>
