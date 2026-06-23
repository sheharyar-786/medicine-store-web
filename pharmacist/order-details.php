<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToPharmacist();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$order_id = (int)$_GET['id'];

// Fetch order header
$query = "SELECT orders.*, users.full_name, users.phone, users.email, users.address as user_address 
          FROM orders 
          JOIN users ON orders.user_id = users.id 
          WHERE orders.id = $order_id LIMIT 1";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: dashboard.php');
    exit();
}

// Fetch order items
$items_query = "SELECT order_items.*, products.name, products.generic_name, products.image_url, products.requires_prescription 
                FROM order_items 
                LEFT JOIN products ON order_items.product_id = products.id 
                WHERE order_items.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

$pageTitle = "Review Order #" . $order_id;
include '../includes/header.php';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="dashboard.php" style="color: var(--primary); font-weight: 600;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark); margin-top: 8px;">Order Details #<?php echo $order_id; ?></h1>
            <p style="color: var(--muted); font-size: 0.9rem;">Placed on <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
        </div>
        <div>
            <span class="status-badge status-<?php echo $order['status']; ?>" style="font-size: 1rem; padding: 6px 16px; border-radius: var(--radius); font-weight: 700;">
                Status: <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
            </span>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
    <div class="alert alert-success" style="margin-bottom: 24px;">Order updated successfully.</div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        <!-- Left Column: Items and Prescription -->
        <div>
            <!-- Order Items -->
            <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 30px;">
                <h3 style="margin-bottom: 16px; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <i class="fas fa-pills" style="color: var(--primary);"></i> Medicines In Order
                </h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); color: var(--muted); font-size: 0.85rem;">
                                <th style="padding: 10px;">Item</th>
                                <th style="padding: 10px;">Generic Name</th>
                                <th style="padding: 10px; text-align: right;">Price</th>
                                <th style="padding: 10px; text-align: center;">Qty</th>
                                <th style="padding: 10px; text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($items_result, 0);
                            while ($item = mysqli_fetch_assoc($items_result)): 
                                $subtotal = $item['price_at_time_of_purchase'] * $item['quantity'];
                            ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 14px 10px;">
                                    <strong><?php echo clean($item['name']); ?></strong>
                                    <?php if ($item['requires_prescription']): ?>
                                    <span style="background: var(--danger); color: white; padding: 1px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; margin-left: 6px;">Rx</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 14px 10px; font-size: 0.85rem; color: var(--muted); font-style: italic;">
                                    <?php echo clean($item['generic_name'] ?: 'N/A'); ?>
                                </td>
                                <td style="padding: 14px 10px; text-align: right; font-size: 0.95rem;">
                                    <?php echo formatPrice($item['price_at_time_of_purchase']); ?>
                                </td>
                                <td style="padding: 14px 10px; text-align: center; font-size: 0.95rem;">
                                    x<?php echo $item['quantity']; ?>
                                </td>
                                <td style="padding: 14px 10px; text-align: right; font-weight: 700; font-size: 0.95rem;">
                                    <?php echo formatPrice($subtotal); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <tr style="font-weight: 800; font-size: 1.1rem; color: var(--dark);">
                                <td colspan="4" style="padding: 20px 10px 10px; text-align: right;">Total Amount:</td>
                                <td style="padding: 20px 10px 10px; text-align: right; color: var(--accent-dark);">
                                    <?php echo formatPrice($order['total_amount']); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Prescription Verification Section -->
            <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 16px; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <i class="fas fa-file-prescription" style="color: var(--primary);"></i> Prescription Verification
                </h3>
                <?php if (!empty($order['prescription_path'])): ?>
                    <div style="background: #f1f5f9; padding: 16px; border-radius: var(--radius); text-align: center; margin-bottom: 20px;">
                        <p style="margin-bottom: 12px; font-size: 0.9rem; color: var(--muted);">Click the image below to view full-screen.</p>
                        <a href="../assets/uploads/prescriptions/<?php echo clean($order['prescription_path']); ?>" target="_blank">
                            <img src="../assets/uploads/prescriptions/<?php echo clean($order['prescription_path']); ?>" 
                                 alt="Uploaded Prescription" 
                                 style="max-width: 100%; max-height: 400px; border-radius: var(--radius); box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 3px solid white; transition: transform 0.2s;"
                                 onmouseover="this.style.transform='scale(1.02)'"
                                 onmouseout="this.style.transform='scale(1)'">
                        </a>
                    </div>
                <?php else: ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 20px; border-radius: var(--radius); text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; color: var(--danger); margin-bottom: 10px;"></i>
                        <h4 style="font-weight: 700; margin-bottom: 4px;">No Prescription Uploaded</h4>
                        <p style="font-size: 0.9rem;">Customer checked out without supplying a prescription file. Verification is required before dispensing Rx products.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Customer Info & Actions -->
        <div>
            <!-- Customer Card -->
            <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 30px;">
                <h3 style="margin-bottom: 16px; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <i class="fas fa-user-circle" style="color: var(--primary);"></i> Customer & Delivery
                </h3>
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.95rem; line-height: 1.5;">
                    <p><strong>Name:</strong><br><?php echo clean($order['full_name']); ?></p>
                    <p><strong>Phone:</strong><br><a href="tel:<?php echo clean($order['phone']); ?>" style="color: var(--primary); font-weight: 600;"><?php echo clean($order['phone']); ?></a></p>
                    <p><strong>Email:</strong><br><?php echo clean($order['email']); ?></p>
                    <p>
                        <strong>Fulfillment:</strong><br>
                        <span style="background: var(--primary-light); color: var(--primary); padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            <?php echo $order['delivery_method'] === 'pickup' ? 'Click & Collect (Store Pickup)' : 'Home Delivery'; ?>
                        </span>
                    </p>
                    <?php if ($order['delivery_method'] === 'delivery'): ?>
                    <p><strong>Shipping Address:</strong><br><?php echo clean($order['shipping_address'] ?: $order['user_address']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pharmacist Decision Box -->
            <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); border: 2px solid var(--primary-light);">
                <h3 style="margin-bottom: 16px; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <i class="fas fa-edit" style="color: var(--primary);"></i> Process Action
                </h3>

                <form action="update_order.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                    <?php if ($order['status'] === 'pending_review'): ?>
                        <!-- Gating verification form -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Prescription Verification:</label>
                            <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 12px; line-height: 1.4;">
                                Please inspect the image file. Ensure that the patient name, date, medication names, and doctor signature are valid and matching.
                            </p>
                        </div>
                        <button type="submit" name="action" value="approve" class="btn btn-block" style="background-color: var(--accent-dark); border-color: var(--accent-dark); color: white; margin-bottom: 12px;">
                            <i class="fas fa-check-circle"></i> Approve Prescription
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-block btn-outline" style="color: var(--danger); border-color: var(--danger); background: transparent;">
                            <i class="fas fa-times-circle"></i> Reject Order
                        </button>

                    <?php elseif ($order['status'] === 'approved'): ?>
                        <!-- Packing step form -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Order Preparation:</label>
                            <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 12px; line-height: 1.4;">
                                Gather all listed items in the quantities requested. Check expiry dates on all packaging before sealing.
                            </p>
                        </div>
                        <button type="submit" name="action" value="pack" class="btn btn-block" style="background-color: var(--primary); color: white;">
                            <i class="fas fa-box-open"></i> Mark as Packed & Ready
                        </button>

                    <?php elseif ($order['status'] === 'packed'): ?>
                        <div style="text-align: center; padding: 12px 0;">
                            <i class="fas fa-check-double" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 10px;"></i>
                            <h4 style="font-weight: 700; color: var(--dark);">Packed & Prepared</h4>
                            <p style="font-size: 0.85rem; color: var(--muted); margin-top: 4px;">
                                <?php if ($order['delivery_method'] === 'pickup'): ?>
                                    Order is sitting in store shelves waiting for Click & Collect customer pickup.
                                <?php else: ?>
                                    Waiting for Driver assignment and pickup.
                                <?php endif; ?>
                            </p>
                        </div>

                    <?php else: ?>
                        <div style="text-align: center; padding: 12px 0; color: var(--muted);">
                            <p><i class="fas fa-info-circle"></i> This order has been finalized (status: <?php echo ucfirst($order['status']); ?>).</p>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
