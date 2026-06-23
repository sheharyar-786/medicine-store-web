<?php
$pageTitle = 'Shopping Cart';
$pageStyles = ['cart'];
include 'includes/header.php';
include 'includes/db_connect.php';

// Drug interaction scanning logic
$conflicts = [];
if (!empty($_SESSION['cart']) && count($_SESSION['cart']) > 1) {
    $generics = [];
    $name_map = [];
    foreach ($_SESSION['cart'] as $item) {
        if (!empty($item['generic_name'])) {
            $g = trim(strtolower($item['generic_name']));
            $generics[] = $g;
            $name_map[$g] = $item['name'];
        }
    }
    
    if (count($generics) > 1) {
        $escaped_generics = array_map(function($g) use ($conn) {
            return mysqli_real_escape_string($conn, $g);
        }, $generics);
        $placeholders = implode("','", $escaped_generics);
        
        $conflict_query = "SELECT * FROM drug_conflicts 
                           WHERE LOWER(generic_name_1) IN ('$placeholders') 
                             AND LOWER(generic_name_2) IN ('$placeholders')";
        $conflict_res = mysqli_query($conn, $conflict_query);
        
        if ($conflict_res && mysqli_num_rows($conflict_res) > 0) {
            while ($c = mysqli_fetch_assoc($conflict_res)) {
                $g1 = strtolower($c['generic_name_1']);
                $g2 = strtolower($c['generic_name_2']);
                
                // Double check to make sure both medications are in the cart (handling edge cases)
                if (in_array($g1, $generics) && in_array($g2, $generics)) {
                    $conflicts[] = [
                        'med1' => $name_map[$g1],
                        'med2' => $name_map[$g2],
                        'severity' => $c['severity'],
                        'message' => $c['warning_message']
                    ];
                }
            }
        }
    }
}
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
    
    <?php if (!empty($conflicts)): ?>
        <?php foreach ($conflicts as $conflict): ?>
        <div class="alert alert-error interaction-alert reveal-init" style="background: #fff5f5; border-left: 5px solid var(--danger); padding: 18px; border-radius: var(--radius); margin-bottom: 24px; display: block;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 1.5rem; margin-top: 2px;"></i>
                <div>
                    <h4 style="color: #991b1b; font-weight: 700; margin-bottom: 4px; font-size: 1rem;">
                        Drug Interaction Alert (<?php echo ucfirst($conflict['severity']); ?> Severity)
                    </h4>
                    <p style="color: #7f1d1d; font-size: 0.9rem; line-height: 1.5; margin: 0;">
                        A potential reaction has been detected between <strong><?php echo clean($conflict['med1']); ?></strong> and <strong><?php echo clean($conflict['med2']); ?></strong>.
                        <br>
                        <strong>Warning:</strong> <?php echo clean($conflict['message']); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

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
