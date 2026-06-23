<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'Checkout';
$pageStyles = ['checkout'];
include 'includes/header.php';

$total = 0;
$requiresPrescription = false;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
        if (!empty($item['requires_prescription'])) {
            $requiresPrescription = true;
        }
    }
}
?>

<main class="container checkout-page">
    <div class="page-header">
        <h1>Finalize Your Order</h1>
        <p>Confirm delivery details and upload prescription if needed</p>
    </div>

    <?php if (isset($_GET['reorder']) && $_GET['reorder'] === 'success'): ?>
    <div class="alert alert-success reveal-init" style="margin-bottom: 24px; display: flex;">
        <i class="fas fa-check-circle"></i> Items from your previous order have been successfully loaded. Review details below to complete your refill!
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error reveal-init" style="margin-bottom: 24px; display: flex;">
        <i class="fas fa-exclamation-circle"></i> <?php echo clean($_GET['error']); ?>
    </div>
    <?php endif; ?>

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
                    <h3><i class="fas fa-shipping-fast"></i> Fulfillment Method</h3>
                    <div class="form-group" style="display: flex; gap: 24px; margin-top: 12px; margin-bottom: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer;">
                            <input type="radio" name="delivery_method" value="delivery" checked onclick="toggleDeliveryMethod('delivery')">
                            <span><i class="fas fa-truck"></i> Home Delivery</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer;">
                            <input type="radio" name="delivery_method" value="pickup" onclick="toggleDeliveryMethod('pickup')">
                            <span><i class="fas fa-store"></i> In-Store Pickup</span>
                        </label>
                    </div>
                </div>

                <div class="checkout-card reveal-init" id="delivery-address-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                    <div class="form-group">
                        <label for="shipping_address">Delivery Address</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" placeholder="Enter your full delivery address" required></textarea>
                    </div>
                </div>

                <div class="checkout-card reveal-init">
                    <h3><i class="fas fa-file-medical"></i> Prescription Upload</h3>
                    <?php if ($requiresPrescription): ?>
                    <div class="prescription-warning-banner" style="background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px 16px; border-radius: var(--radius); margin-bottom: 16px; display: flex; align-items: center; gap: 10px; font-size: 0.85rem; line-height: 1.4;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.2rem; color: #d97706; flex-shrink: 0;"></i>
                        <span><strong>Prescription Required:</strong> Your cart contains Rx-only medication. An upload of your prescription is <strong>mandatory</strong>.</span>
                    </div>
                    <?php else: ?>
                    <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 16px;">
                        If your order contains medicines requiring a prescription, please upload it below (JPG, PNG, or PDF).
                    </p>
                    <?php endif; ?>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary); margin-bottom: 8px;"></i>
                        <p>Drag & drop or click to upload</p>
                        <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.pdf" <?php echo $requiresPrescription ? 'required' : ''; ?>>
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

<script>
function toggleDeliveryMethod(method) {
    const addressCard = document.getElementById('delivery-address-card');
    const addressInput = document.getElementById('shipping_address');
    if (method === 'pickup') {
        addressCard.style.display = 'none';
        addressInput.value = 'In-Store Pickup';
        addressInput.required = false;
    } else {
        addressCard.style.display = 'block';
        addressInput.value = '';
        addressInput.required = true;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
