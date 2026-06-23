<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToAdmin();

$simulated_message = '';

// Handle simulation trigger
if (isset($_POST['send_reminder'])) {
    $reminder_id = (int)$_POST['reminder_id'];

    $rem_q = mysqli_query($conn, "SELECT refill_reminders.*, users.full_name, users.phone, users.email 
                                  FROM refill_reminders 
                                  JOIN users ON refill_reminders.user_id = users.id 
                                  WHERE refill_reminders.id = $reminder_id LIMIT 1");
    $reminder = mysqli_fetch_assoc($rem_q);

    if ($reminder) {
        $now = date('Y-m-d H:i:s');
        $next_date = date('Y-m-d', strtotime('+30 days'));
        
        // Update database log
        $update_sql = "UPDATE refill_reminders 
                       SET last_reminder_sent = '$now', next_reminder_date = '$next_date' 
                       WHERE id = $reminder_id";
        
        if (mysqli_query($conn, $update_sql)) {
            // Generate simulated SMS/Email body text
            $domain = "http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . basePath();
            $reorder_link = $domain . "/reorder.php?order_id=" . $reminder['order_id'];
            
            $simulated_message = [
                'to' => $reminder['full_name'] . " (" . ($reminder['phone'] ?: $reminder['email']) . ")",
                'message' => "Time for your refill! Click here to reorder in 1 click: " . $reorder_link
            ];
            $msg = "success";
        }
    }
}

$pageTitle = 'Refill Reminders';
include '../includes/header.php';

// Query scheduled refill reminders
$reminders_query = "SELECT refill_reminders.*, users.full_name, users.phone, users.email, orders.total_amount 
                    FROM refill_reminders 
                    JOIN users ON refill_reminders.user_id = users.id 
                    JOIN orders ON refill_reminders.order_id = orders.id 
                    ORDER BY refill_reminders.next_reminder_date ASC";
$reminders_result = mysqli_query($conn, $reminders_query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Automated Refill Reminders</h1>
            <p>Monitor patient chronic cycles and send simulated 1-click reorder notifications.</p>
        </div>

        <?php if (!empty($simulated_message)): ?>
        <div class="alert alert-success" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; display: block; padding: 20px; border-radius: var(--radius); margin-bottom: 30px;">
            <h4 style="font-weight: 800; margin-bottom: 8px; font-size: 1rem;"><i class="fas fa-paper-plane"></i> Simulated Alert Dispatched!</h4>
            <div style="background: white; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px; font-family: monospace; font-size: 0.9rem; line-height: 1.5; color: var(--dark);">
                <strong>Recipient:</strong> <?php echo clean($simulated_message['to']); ?><br>
                <strong>Text Message:</strong> <?php echo clean($simulated_message['message']); ?>
            </div>
            <p style="font-size: 0.8rem; margin-top: 8px; color: #0284c7; font-style: italic;">
                *Note: In production, this would trigger an SMS gateway or SendGrid SMTP relay to the patient's device.*
            </p>
        </div>
        <?php endif; ?>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>Chronic Refill Cycles</h2>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reminder ID</th>
                        <th>Patient</th>
                        <th>Reference Order</th>
                        <th>Next Refill Due</th>
                        <th>Last Alert Sent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reminders_result) > 0): ?>
                        <?php while ($rem = mysqli_fetch_assoc($reminders_result)): 
                            $due_soon = strtotime($rem['next_reminder_date']) <= strtotime('+3 days');
                        ?>
                        <tr style="<?php if($due_soon) echo 'background-color: #fffbeb;'; ?>">
                            <td>#<?php echo $rem['id']; ?></td>
                            <td>
                                <strong><?php echo clean($rem['full_name']); ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--muted);"><?php echo clean($rem['phone'] ?: $rem['email']); ?></span>
                            </td>
                            <td>
                                <a href="order-details.php?id=<?php echo $rem['order_id']; ?>">Order #<?php echo $rem['order_id']; ?></a>
                                (<?php echo formatPrice($rem['total_amount']); ?>)
                            </td>
                            <td>
                                <span style="font-weight: 700; color: <?php echo $due_soon ? 'var(--warning)' : 'inherit'; ?>">
                                    <?php echo date('M d, Y', strtotime($rem['next_reminder_date'])); ?>
                                </span>
                                <?php if ($due_soon): ?>
                                <span style="background: #fef3c7; color: #d97706; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 6px;">Due Soon</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--muted);">
                                <?php echo $rem['last_reminder_sent'] ? date('M d, Y h:i A', strtotime($rem['last_reminder_sent'])) : 'Never'; ?>
                            </td>
                            <td>
                                <form action="refill-reminders.php" method="POST">
                                    <input type="hidden" name="reminder_id" value="<?php echo $rem['id']; ?>">
                                    <button type="submit" name="send_reminder" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-paper-plane"></i> Send Alert
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No refill reminder logs found. Sell chronic medicines to automatically trigger cycles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
