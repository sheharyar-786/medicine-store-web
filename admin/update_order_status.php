<?php
include '../includes/db_connect.php';

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: view-orders.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>