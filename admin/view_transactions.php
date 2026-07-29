<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['role']=="admin"){
    
$sql = "select * from transactions";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "Error!: {$result->error}";
}
else{

}
}
else{
    header("Location:../dashboard.php");
}
}else{
    header("Location:../login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>library</title>
    <link rel="stylesheet" href="../style.css">
    <style type="text/css">
        .update{
            text-decration: none;
            background-color: green;
            color: white;
        }
        .delete{
            text-decration: none;
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <table class="view_books">
    <thead>
        <tr>
            <th>user id</th>
            <th>book id</th>
            <th>issue date</th>
            <th>return date</th>
            <th>status</th>
            <th>Action</th>
            <th>action</th>
        </tr>
    </thead>
    <tbody>
       <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['book_id']; ?></td>
        <td><?php echo $row['issue_date']; ?></td>
        <td><?php echo $row['return_date']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><a href="update_transactions.php?transaction_id=<?php echo $row['id']?>" class="update">Update</a></td>
        <td><a href="delete_transactions.php?transaction_id=<?php echo $row['id']?>"class="delete">Delete</a></td>
    </tr>
<?php } ?>

        
    </tbody>
</table>
</body>
</html>