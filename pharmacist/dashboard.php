<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToPharmacist();

$pageTitle = 'Pharmacist Dashboard';
include '../includes/header.php';

// Fetch orders needing prescription review first
$pending_review_query = "SELECT orders.*, users.full_name, users.phone FROM orders 
                         JOIN users ON orders.user_id = users.id 
                         WHERE orders.status = 'pending_review' 
                         ORDER BY orders.order_date ASC";
$pending_review_result = mysqli_query($conn, $pending_review_query);

// Fetch orders that are approved and need to be packed/ready
$approved_query = "SELECT orders.*, users.full_name FROM orders 
                   JOIN users ON orders.user_id = users.id 
                   WHERE orders.status = 'approved' 
                   ORDER BY orders.order_date ASC";
$approved_result = mysqli_query($conn, $approved_query);

// Fetch all other orders for reference
$other_query = "SELECT orders.*, users.full_name FROM orders 
                JOIN users ON orders.user_id = users.id 
                WHERE orders.status NOT IN ('pending_review', 'approved') 
                ORDER BY orders.order_date DESC LIMIT 15";
$other_result = mysqli_query($conn, $other_query);
?>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <div class="page-header" style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 800; color: var(--primary);"><i class="fas fa-prescription-bottle-alt"></i> Pharmacist Hub</h1>
        <p>Verify prescription orders and prepare packets for dispensing.</p>
    </div>

    <!-- Section 1: Pending Pharmacist Review (Critical Rx gating) -->
    <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 30px; border-left: 5px solid var(--warning);">
        <h3 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--dark); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-medical-alt" style="color: var(--warning);"></i> 
            Pending Rx Verification 
            <span style="background: var(--warning-bg); color: var(--warning); padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 700; margin-left: auto;">
                <?php echo mysqli_num_rows($pending_review_result); ?> orders
            </span>
        </h3>
        
        <?php if (mysqli_num_rows($pending_review_result) > 0): ?>
        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); padding-bottom: 12px;">
                    <th style="padding: 12px;">Order ID</th>
                    <th style="padding: 12px;">Customer</th>
                    <th style="padding: 12px;">Total</th>
                    <th style="padding: 12px;">Method</th>
                    <th style="padding: 12px;">Prescription</th>
                    <th style="padding: 12px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($pending_review_result)): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px 12px;"><strong>#<?php echo $order['id']; ?></strong></td>
                    <td style="padding: 16px 12px;"><?php echo clean($order['full_name']); ?></td>
                    <td style="padding: 16px 12px;"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td style="padding: 16px 12px;">
                        <span style="font-size: 0.85rem; font-weight: 500;">
                            <?php echo $order['delivery_method'] === 'pickup' ? '<i class="fas fa-store"></i> Pickup' : '<i class="fas fa-truck"></i> Delivery'; ?>
                        </span>
                    </td>
                    <td style="padding: 16px 12px;">
                        <?php if (!empty($order['prescription_path'])): ?>
                        <a href="../assets/uploads/prescriptions/<?php echo clean($order['prescription_path']); ?>" target="_blank" style="color: var(--primary); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fas fa-image"></i> View File
                        </a>
                        <?php else: ?>
                        <span style="color: var(--danger);"><i class="fas fa-times-circle"></i> Missing</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 12px;">
                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm">Review & Verify</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: var(--muted); text-align: center; padding: 24px 0;">No prescription orders waiting verification. Good job!</p>
        <?php endif; ?>
    </div>

    <!-- Section 2: Approved Orders Ready for Packing -->
    <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 30px; border-left: 5px solid var(--accent-dark);">
        <h3 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--dark); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-box" style="color: var(--accent-dark);"></i> 
            Approved Orders Needs Packing
            <span style="background: var(--primary-light); color: var(--primary); padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 700; margin-left: auto;">
                <?php echo mysqli_num_rows($approved_result); ?> orders
            </span>
        </h3>
        
        <?php if (mysqli_num_rows($approved_result) > 0): ?>
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border);">
                    <th style="padding: 12px; text-align: left;">Order ID</th>
                    <th style="padding: 12px; text-align: left;">Customer</th>
                    <th style="padding: 12px; text-align: left;">Total</th>
                    <th style="padding: 12px; text-align: left;">Method</th>
                    <th style="padding: 12px; text-align: left;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($approved_result)): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px 12px;"><strong>#<?php echo $order['id']; ?></strong></td>
                    <td style="padding: 16px 12px;"><?php echo clean($order['full_name']); ?></td>
                    <td style="padding: 16px 12px;"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td style="padding: 16px 12px;">
                        <span style="font-size: 0.85rem; font-weight: 500;">
                            <?php echo $order['delivery_method'] === 'pickup' ? '<i class="fas fa-store"></i> Pickup' : '<i class="fas fa-truck"></i> Delivery'; ?>
                        </span>
                    </td>
                    <td style="padding: 16px 12px;">
                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">Prepare Packet</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: var(--muted); text-align: center; padding: 24px 0;">All approved orders are packed and prepared.</p>
        <?php endif; ?>
    </div>

    <!-- Section 3: History & Processed Orders -->
    <div class="card reveal-init" style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow);">
        <h3 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--dark);"><i class="fas fa-history"></i> Recently Processed</h3>
        
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border);">
                    <th style="padding: 12px; text-align: left;">Order ID</th>
                    <th style="padding: 12px; text-align: left;">Customer</th>
                    <th style="padding: 12px; text-align: left;">Total</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($other_result)): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px 12px;"><strong>#<?php echo $order['id']; ?></strong></td>
                    <td style="padding: 16px 12px;"><?php echo clean($order['full_name']); ?></td>
                    <td style="padding: 16px 12px;"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td style="padding: 16px 12px;">
                        <span class="status-badge status-<?php echo $order['status']; ?>" style="padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td style="padding: 16px 12px;">
                        <a href="order-details.php?id=<?php echo $order['id']; ?>" style="color: var(--primary); font-weight: 500;">View Details</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
