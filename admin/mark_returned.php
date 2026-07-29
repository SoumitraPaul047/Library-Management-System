<?php
include "../db.php";

if(isset($_POST['transaction_id'])) {
    $transaction_id = intval($_POST['transaction_id']);

    // Get the book_id from the transaction
    $get_sql = "SELECT book_id FROM transactions WHERE id = $transaction_id";
    $get_result = mysqli_query($conn, $get_sql);

    if($get_result && mysqli_num_rows($get_result) > 0) {
        $row = mysqli_fetch_assoc($get_result);
        $book_id = $row['book_id'];

        $return_date = date('Y-m-d');

        // Update transaction: set status to returned and return_date to today
        $update_transaction = "UPDATE transactions 
                               SET status = 'returned', return_date = '$return_date' 
                               WHERE id = $transaction_id";

        // Increase book quantity by 1
        $update_quantity = "UPDATE books 
                            SET quantity = quantity + 1 
                            WHERE id = $book_id";

        if(mysqli_query($conn, $update_transaction) && mysqli_query($conn, $update_quantity)) {
            echo "success";
        } else {
            echo "error";
        }

    } else {
        echo "error";
    }
}
?>