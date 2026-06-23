<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'Checkout';
include 'includes/functions.php';
include 'includes/header.php';

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<main class="container checkout-page">
    <div class="page-header">
        <h1>Finalize Your Order</h1>
        <p>Confirm delivery details and upload prescription if needed</p>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
    <div class="empty-state">
        <i class="fas fa-shopping-cart"></i>
        <h3>Your cart is empty</h3>
        <a href="shop.php" class="btn" style="margin-top: 20px;">Browse Medicines</a>
    </div>
    <?php else: ?>

    <form action="actions/place_order_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

        <div class="checkout-grid">
            <div>
                <div class="checkout-card reveal-init">
                    <h3><i class="fas fa-truck"></i> Delivery Details</h3>
                    <div class="form-group">
                        <label for="shipping_address">Delivery Address</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" placeholder="Enter your full delivery address" required></textarea>
                    </div>
                </div>

                <div class="checkout-card reveal-init">
                    <h3><i class="fas fa-file-medical"></i> Prescription Upload</h3>
                    <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 16px;">
                        If your order contains medicines requiring a prescription, please upload it below (JPG, PNG, or PDF).
                    </p>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary); margin-bottom: 8px;"></i>
                        <p>Drag & drop or click to upload</p>
                        <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
            </div>

            <div class="order-summary-card reveal-init">
                <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Order Summary</h3>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="summary-row">
                    <span><?php echo clean($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                    <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-total">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <button type="submit" name="place_order" class="btn btn-block" style="margin-top: 24px;">
                    <i class="fas fa-check-circle"></i> Confirm Order
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
