<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToDriver();

$pageTitle = 'Driver Deliveries';
include '../includes/header.php';

$driver_id = (int)$_SESSION['user_id'];

// Query assigned active deliveries (packed and out_for_delivery)
$delivery_query = "SELECT orders.*, users.full_name, users.phone, users.address as user_address 
                   FROM orders 
                   JOIN users ON orders.user_id = users.id 
                   WHERE orders.driver_id = $driver_id 
                     AND orders.status IN ('packed', 'out_for_delivery') 
                   ORDER BY orders.status DESC, orders.order_date ASC";
$delivery_result = mysqli_query($conn, $delivery_query);

// Query history of driver's deliveries
$history_query = "SELECT orders.*, users.full_name FROM orders 
                  JOIN users ON orders.user_id = users.id 
                  WHERE orders.driver_id = $driver_id 
                    AND orders.status = 'delivered' 
                  ORDER BY orders.order_date DESC LIMIT 10";
$history_result = mysqli_query($conn, $history_query);
?>

<!-- Mobile Optimized Layout -->
<div class="container" style="max-width: 600px; margin-top: 20px; margin-bottom: 60px; padding: 0 16px;">
    
    <div style="background: white; border-radius: var(--radius); padding: 18px; box-shadow: var(--shadow); margin-bottom: 24px; text-align: center; border-bottom: 4px solid var(--primary);">
        <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fas fa-motorcycle"></i> Driver Portal
        </h2>
        <p style="color: var(--muted); font-size: 0.85rem; margin-top: 4px;">Welcome, <?php echo clean($_SESSION['user_name']); ?>. Keep safe on the roads!</p>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success" style="font-size: 0.85rem; padding: 10px 14px; margin-bottom: 20px;">Delivery status updated.</div>
    <?php endif; ?>

    <h3 style="font-size: 1rem; color: var(--dark); margin-bottom: 12px; font-weight: 700;">Active Shipments (<?php echo mysqli_num_rows($delivery_result); ?>)</h3>

    <?php if (mysqli_num_rows($delivery_result) > 0): ?>
        <?php while ($delivery = mysqli_fetch_assoc($delivery_result)): 
            $destAddress = $delivery['shipping_address'] ?: $delivery['user_address'];
            $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($destAddress);
        ?>
        <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 18px; box-shadow: var(--shadow); margin-bottom: 16px; border-left: 5px solid <?php echo $delivery['status'] === 'out_for_delivery' ? 'var(--accent)' : 'var(--primary)'; ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-weight: 700; font-size: 0.95rem; color: var(--dark);">Order #<?php echo $delivery['id']; ?></span>
                <span class="status-badge status-<?php echo $delivery['status']; ?>" style="font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; font-weight: 700;">
                    <?php echo $delivery['status'] === 'out_for_delivery' ? 'Out for Delivery' : 'Ready (Packed)'; ?>
                </span>
            </div>

            <!-- Customer Details -->
            <div style="font-size: 0.9rem; margin-bottom: 16px; color: var(--text); line-height: 1.5;">
                <p style="margin-bottom: 6px;"><i class="fas fa-user" style="color: var(--muted); width: 20px;"></i> <strong><?php echo clean($delivery['full_name']); ?></strong></p>
                <p style="margin-bottom: 6px;"><i class="fas fa-phone" style="color: var(--muted); width: 20px;"></i> <a href="tel:<?php echo clean($delivery['phone']); ?>" style="color: var(--primary); font-weight: 600;"><?php echo clean($delivery['phone']); ?></a></p>
                <p style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 4px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--danger); width: 20px; margin-top: 3px; flex-shrink: 0;"></i> 
                    <span><?php echo clean($destAddress); ?></span>
                </p>
                <p style="margin-bottom: 6px;"><i class="fas fa-wallet" style="color: var(--muted); width: 20px;"></i> Collect Amount: <strong style="color: var(--accent-dark);"><?php echo formatPrice($delivery['total_amount']); ?></strong></p>
            </div>

            <!-- Actions row -->
            <div style="display: flex; gap: 8px;">
                <a href="<?php echo $mapsUrl; ?>" target="_blank" class="btn btn-sm btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.8rem;">
                    <i class="fas fa-directions"></i> Get Route
                </a>
                <form action="update_delivery.php" method="POST" style="flex: 1.5; display: flex;">
                    <input type="hidden" name="order_id" value="<?php echo $delivery['id']; ?>">
                    <?php if ($delivery['status'] === 'packed'): ?>
                    <button type="submit" name="action" value="start" class="btn btn-sm btn-block" style="font-size: 0.8rem; height: 100%;">
                        <i class="fas fa-play"></i> Start Delivery
                    </button>
                    <?php else: ?>
                    <button type="submit" name="action" value="deliver" class="btn btn-sm btn-block" style="background-color: var(--accent-dark); border-color: var(--accent-dark); color: white; font-size: 0.8rem; height: 100%;">
                        <i class="fas fa-check"></i> Mark Delivered
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="background: white; border-radius: var(--radius); padding: 30px; text-align: center; box-shadow: var(--shadow); margin-bottom: 30px;">
            <i class="fas fa-clipboard-check" style="font-size: 2.5rem; color: var(--muted); margin-bottom: 10px;"></i>
            <h4 style="color: var(--dark); font-weight: 700;">No Active Deliveries</h4>
            <p style="font-size: 0.85rem; color: var(--muted); margin-top: 4px;">Wait for the pharmacist or admin to assign orders to you.</p>
        </div>
    <?php endif; ?>

    <!-- History Section -->
    <h3 style="font-size: 1rem; color: var(--dark); margin-top: 30px; margin-bottom: 12px; font-weight: 700;">Delivered Today (<?php echo mysqli_num_rows($history_result); ?>)</h3>
    <div style="background: white; border-radius: var(--radius); padding: 12px; box-shadow: var(--shadow);">
        <?php if (mysqli_num_rows($history_result) > 0): ?>
            <ul style="padding: 0; margin: 0; list-style: none;">
                <?php while ($hist = mysqli_fetch_assoc($history_result)): ?>
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 12px 6px; border-bottom: 1px solid var(--border); font-size: 0.85rem;">
                    <span><strong>Order #<?php echo $hist['id']; ?></strong> — <?php echo clean($hist['full_name']); ?></span>
                    <span style="color: var(--accent-dark); font-weight: 700;"><?php echo formatPrice($hist['total_amount']); ?></span>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p style="color: var(--muted); text-align: center; font-size: 0.8rem; padding: 12px 0;">No completed orders logged for this shift.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
