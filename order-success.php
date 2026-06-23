<?php
$pageTitle = 'Order Confirmed';
$pageStyles = ['success'];
include 'includes/header.php';
$orderId = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';
?>

<main class="container">
    <div class="success-page reveal-init">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h1>Order Placed Successfully!</h1>
        <?php if ($orderId): ?>
        <p>Your order <strong>#<?php echo $orderId; ?></strong> is currently pending. If you uploaded a prescription, our pharmacist will review it shortly.</p>
        <?php else: ?>
        <p>Your order is currently pending. If you uploaded a prescription, our pharmacist will review it shortly.</p>
        <?php endif; ?>
        <div class="success-actions">
            <a href="index.php" class="btn"><i class="fas fa-home"></i> Return Home</a>
            <a href="my-orders.php" class="btn btn-outline"><i class="fas fa-truck"></i> Track Order</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
