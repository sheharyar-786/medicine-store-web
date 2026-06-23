<?php
$pageTitle = 'Shopping Cart';
include 'includes/functions.php';
include 'includes/header.php';
?>

<main class="container cart-page">
    <div class="page-header">
        <h1>Your Shopping Cart</h1>
        <p>Review your items before checkout</p>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
    <div class="empty-state reveal-init">
        <i class="fas fa-shopping-cart"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added any medicines yet.</p>
        <a href="shop.php" class="btn" style="margin-top: 20px;"><i class="fas fa-pills"></i> Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="cart-table-wrap reveal-init">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><strong><?php echo clean($item['name']); ?></strong></td>
                    <td><?php echo formatPrice($item['price']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><strong><?php echo formatPrice($subtotal); ?></strong></td>
                    <td><a href="actions/remove_item.php?id=<?php echo $id; ?>" class="remove-link"><i class="fas fa-trash-alt"></i> Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="cart-summary reveal-init">
        <div class="cart-total">Total: <span><?php echo formatPrice($total); ?></span></div>
        <a href="checkout.php" class="btn"><i class="fas fa-lock"></i> Proceed to Checkout</a>
    </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
