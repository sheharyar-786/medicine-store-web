<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToAdmin();

$pageTitle = 'Sales Reports';
include '../includes/header.php';

// 1. Total revenue & orders count (Delivered)
$summary_q = mysqli_query($conn, "SELECT SUM(total_amount) as revenue, COUNT(id) as total_count FROM orders WHERE status = 'delivered'");
$summary = mysqli_fetch_assoc($summary_q);
$total_revenue = $summary['revenue'] ?: 0;
$total_delivered = $summary['total_count'] ?: 0;

// 2. Average Order Value
$avg_order = $total_delivered > 0 ? $total_revenue / $total_delivered : 0;

// 3. Breakdown of delivery methods
$delivery_method_q = mysqli_query($conn, "SELECT delivery_method, COUNT(id) as cnt, SUM(total_amount) as rev FROM orders GROUP BY delivery_method");
$delivery_methods = [];
while ($row = mysqli_fetch_assoc($delivery_method_q)) {
    $delivery_methods[$row['delivery_method']] = $row;
}

$delivery_count = $delivery_methods['delivery']['cnt'] ?? 0;
$delivery_revenue = $delivery_methods['delivery']['rev'] ?? 0;
$pickup_count = $delivery_methods['pickup']['cnt'] ?? 0;
$pickup_revenue = $delivery_methods['pickup']['rev'] ?? 0;

// 4. Breakdown of orders with/without prescription (OTC vs Rx)
$rx_vs_otc_q = mysqli_query($conn, "SELECT 
                                    SUM(CASE WHEN prescription_path IS NOT NULL AND prescription_path != '' THEN 1 ELSE 0 END) as rx_count,
                                    SUM(CASE WHEN prescription_path IS NULL OR prescription_path = '' THEN 1 ELSE 0 END) as otc_count
                                   FROM orders");
$rx_vs_otc = mysqli_fetch_assoc($rx_vs_otc_q);
$rx_count = $rx_vs_otc['rx_count'] ?: 0;
$otc_count = $rx_vs_otc['otc_count'] ?: 0;

// 5. Recent Sales Details
$sales_query = "SELECT orders.*, users.full_name FROM orders 
                JOIN users ON orders.user_id = users.id 
                ORDER BY orders.order_date DESC LIMIT 10";
$sales_result = mysqli_query($conn, $sales_query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Sales Reports</h1>
            <p>Financial summaries and customer order insights.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card reveal-init">
                <div class="stat-icon green"><i class="fas fa-coins"></i></div>
                <div class="stat-info">
                    <h4>Delivered Sales</h4>
                    <h2><?php echo formatPrice($total_revenue); ?></h2>
                </div>
            </div>
            <div class="stat-card reveal-init">
                <div class="stat-icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h4>Completed Deliveries</h4>
                    <h2><?php echo $total_delivered; ?></h2>
                </div>
            </div>
            <div class="stat-card reveal-init">
                <div class="stat-icon orange"><i class="fas fa-calculator"></i></div>
                <div class="stat-info">
                    <h4>Average Order Value</h4>
                    <h2><?php echo formatPrice($avg_order); ?></h2>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; align-items: start;">
            <!-- Delivery Method Breakdown -->
            <div class="admin-card reveal-init">
                <div class="admin-card-header">
                    <h2>Fulfillment Methods</h2>
                </div>
                <div style="padding: 24px;">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                            <span>Home Delivery (<?php echo $delivery_count; ?> orders)</span>
                            <strong><?php echo formatPrice($delivery_revenue); ?></strong>
                        </div>
                        <div style="background: var(--border); height: 12px; border-radius: 6px; overflow: hidden;">
                            <?php 
                            $del_pct = ($total_revenue > 0) ? ($delivery_revenue / ($delivery_revenue + $pickup_revenue)) * 100 : 0;
                            ?>
                            <div style="background: var(--primary); width: <?php echo $del_pct; ?>%; height: 100%;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                            <span>Click & Collect (<?php echo $pickup_count; ?> orders)</span>
                            <strong><?php echo formatPrice($pickup_revenue); ?></strong>
                        </div>
                        <div style="background: var(--border); height: 12px; border-radius: 6px; overflow: hidden;">
                            <?php 
                            $pick_pct = ($total_revenue > 0) ? ($pickup_revenue / ($delivery_revenue + $pickup_revenue)) * 100 : 0;
                            ?>
                            <div style="background: var(--accent-dark); width: <?php echo $pick_pct; ?>%; height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescription vs OTC Orders -->
            <div class="admin-card reveal-init">
                <div class="admin-card-header">
                    <h2>Rx vs OTC Breakdown</h2>
                </div>
                <div style="padding: 24px;">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                            <span>Prescription-Only (Rx Required)</span>
                            <strong><?php echo $rx_count; ?> orders</strong>
                        </div>
                        <div style="background: var(--border); height: 12px; border-radius: 6px; overflow: hidden;">
                            <?php 
                            $rx_pct = ($rx_count + $otc_count > 0) ? ($rx_count / ($rx_count + $otc_count)) * 100 : 0;
                            ?>
                            <div style="background: var(--danger); width: <?php echo $rx_pct; ?>%; height: 100%;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                            <span>OTC (Over the Counter)</span>
                            <strong><?php echo $otc_count; ?> orders</strong>
                        </div>
                        <div style="background: var(--border); height: 12px; border-radius: 6px; overflow: hidden;">
                            <?php 
                            $otc_pct = ($rx_count + $otc_count > 0) ? ($otc_count / ($rx_count + $otc_count)) * 100 : 0;
                            ?>
                            <div style="background: var(--accent-dark); width: <?php echo $otc_pct; ?>%; height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>Recent Financial Transactions</h2>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($sales_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($sales_result)): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><?php echo clean($row['full_name']); ?></td>
                            <td><?php echo formatPrice($row['total_amount']); ?></td>
                            <td>
                                <span style="font-size: 0.85rem; font-weight: 500;">
                                    <?php echo $row['delivery_method'] === 'pickup' ? '<i class="fas fa-store"></i> Pickup' : '<i class="fas fa-truck"></i> Delivery'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No transactions logged.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
