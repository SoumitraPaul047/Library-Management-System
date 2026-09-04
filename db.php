
<?php
 $server = "localhost";
 $user = "SoumitraPaul";
 $pass = "1234567890";
 $dbname = "library_management_system";
 $conn = mysqli_connect('localhost', 'root', '', 'library_management_system');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
 
?>
