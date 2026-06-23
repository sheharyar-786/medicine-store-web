<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/auth_check.php';

// Protect page
restrictToPharmacist();

if (isset($_POST['order_id']) && isset($_POST['action'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];

    // Fetch order details
    $order_q = mysqli_query($conn, "SELECT user_id, status FROM orders WHERE id = $order_id LIMIT 1");
    $order = mysqli_fetch_assoc($order_q);

    if ($order) {
        $user_id = $order['user_id'];
        $new_status = '';

        if ($action === 'approve') {
            $new_status = 'approved';
        } elseif ($action === 'reject') {
            $new_status = 'rejected';
        } elseif ($action === 'pack') {
            $new_status = 'packed';

            // Automatic Refill Reminder Setup:
            // Check if this order contains any chronic condition medications (like diabetes/blood pressure medicines)
            $chronic_q = "SELECT order_items.* FROM order_items 
                          JOIN products ON order_items.product_id = products.id 
                          WHERE order_items.order_id = $order_id AND products.is_chronic = 1";
            $chronic_res = mysqli_query($conn, $chronic_q);

            if ($chronic_res && mysqli_num_rows($chronic_res) > 0) {
                // Schedule the next refill reminder in 30 days
                $next_reminder = date('Y-m-d', strtotime('+30 days'));
                
                // Ensure no duplicate reminders are created for this order
                $check_dup = mysqli_query($conn, "SELECT id FROM refill_reminders WHERE order_id = $order_id");
                if (mysqli_num_rows($check_dup) === 0) {
                    $insert_reminder = "INSERT INTO refill_reminders (user_id, order_id, next_reminder_date, status) 
                                        VALUES ($user_id, $order_id, '$next_reminder', 'active')";
                    mysqli_query($conn, $insert_reminder);
                }
            }
        }

        if (!empty($new_status)) {
            $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
            if (mysqli_query($conn, $update_sql)) {
                header("Location: order-details.php?id=" . $order_id . "&msg=success");
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
