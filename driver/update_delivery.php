<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/auth_check.php';

// Protect page
restrictToDriver();

if (isset($_POST['order_id']) && isset($_POST['action'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];
    $driver_id = (int)$_SESSION['user_id'];

    // Verify order belongs to this driver
    $check_q = mysqli_query($conn, "SELECT id FROM orders WHERE id = $order_id AND driver_id = $driver_id LIMIT 1");
    if (mysqli_num_rows($check_q) > 0) {
        $new_status = '';
        if ($action === 'start') {
            $new_status = 'out_for_delivery';
        } elseif ($action === 'deliver') {
            $new_status = 'delivered';
        }

        if (!empty($new_status)) {
            $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
            if (mysqli_query($conn, $update_sql)) {
                header("Location: dashboard.php?msg=updated");
                exit();
            } else {
                echo "Database Error: " . mysqli_error($conn);
            }
        }
    }
}

header("Location: dashboard.php");
exit();
?>
