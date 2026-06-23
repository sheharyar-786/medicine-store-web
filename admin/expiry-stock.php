<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToAdmin();

// Handle batch submission
if (isset($_POST['add_batch'])) {
    $product_id = (int)$_POST['product_id'];
    $batch_number = mysqli_real_escape_string($conn, $_POST['batch_number']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $quantity = (int)$_POST['quantity'];

    $insert_sql = "INSERT INTO product_batches (product_id, batch_number, expiry_date, quantity) 
                   VALUES ($product_id, '$batch_number', '$expiry_date', $quantity)";
    
    if (mysqli_query($conn, $insert_sql)) {
        // Automatically increment product stock_quantity
        $update_stock = "UPDATE products SET stock_quantity = stock_quantity + $quantity WHERE id = $product_id";
        mysqli_query($conn, $update_stock);
        $msg = "success";
    } else {
        $error = "Error adding batch: " . mysqli_error($conn);
    }
}

$pageTitle = 'Expiry & Stock Alerts';
include '../includes/header.php';

// Query low-stock products (quantity < 10)
$low_stock_query = "SELECT id, name, generic_name, category, price, stock_quantity FROM products WHERE stock_quantity < 10 ORDER BY stock_quantity ASC";
$low_stock_result = mysqli_query($conn, $low_stock_query);

// Query batches expiring in 30 days (expiry_date between today and +30 days)
$expiring_query = "SELECT product_batches.*, products.name FROM product_batches 
                   JOIN products ON product_batches.product_id = products.id 
                   WHERE product_batches.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                     AND product_batches.expiry_date >= CURDATE()
                     AND product_batches.quantity > 0
                   ORDER BY product_batches.expiry_date ASC";
$expiring_result = mysqli_query($conn, $expiring_query);

// Query expired batches (expiry_date < today)
$expired_query = "SELECT product_batches.*, products.name FROM product_batches 
                  JOIN products ON product_batches.product_id = products.id 
                  WHERE product_batches.expiry_date < CURDATE()
                    AND product_batches.quantity > 0
                  ORDER BY product_batches.expiry_date ASC";
$expired_result = mysqli_query($conn, $expired_query);

// Query all products for select input
$products_query = "SELECT id, name FROM products ORDER BY name ASC";
$products_list = mysqli_query($conn, $products_query);

// Query all batch records
$all_batches_query = "SELECT product_batches.*, products.name FROM product_batches 
                      JOIN products ON product_batches.product_id = products.id 
                      ORDER BY product_batches.expiry_date ASC LIMIT 30";
$all_batches_result = mysqli_query($conn, $all_batches_query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Expiry & Stock Tracker</h1>
            <p>Log batches, monitor shelves, and verify product viability.</p>
        </div>

        <?php if (isset($msg) && $msg === 'success'): ?>
        <div class="alert alert-success">Batch logged and stock level updated.</div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo clean($error); ?></div>
        <?php endif; ?>

        <!-- Low Stock Alerts & Expiry Warnings -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; align-items: start;">
            <!-- Low Stock Column -->
            <div class="admin-card reveal-init" style="border-top: 4px solid var(--danger);">
                <div class="admin-card-header">
                    <h2 style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts (< 10 units)</h2>
                </div>
                <div style="padding: 16px;">
                    <?php if (mysqli_num_rows($low_stock_result) > 0): ?>
                        <table class="admin-table" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($prod = mysqli_fetch_assoc($low_stock_result)): ?>
                                <tr>
                                    <td><strong><?php echo clean($prod['name']); ?></strong></td>
                                    <td style="color: var(--danger); font-weight: 700;"><?php echo $prod['stock_quantity']; ?> units</td>
                                    <td><?php echo formatPrice($prod['price']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--muted); text-align: center; padding: 20px;">All medicine stock levels are healthy.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Expiry Warnings Column -->
            <div class="admin-card reveal-init" style="border-top: 4px solid var(--warning);">
                <div class="admin-card-header">
                    <h2 style="color: var(--warning);"><i class="fas fa-hourglass-half"></i> Expiring Soon (30 Days)</h2>
                </div>
                <div style="padding: 16px;">
                    <?php if (mysqli_num_rows($expiring_result) > 0 || mysqli_num_rows($expired_result) > 0): ?>
                        <table class="admin-table" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>Product (Batch)</th>
                                    <th>Expiry</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Show Expired first -->
                                <?php while ($batch = mysqli_fetch_assoc($expired_result)): ?>
                                <tr style="background-color: #fee2e2;">
                                    <td><strong><?php echo clean($batch['name']); ?></strong><br><span style="font-size: 0.8rem; color: var(--muted);">Batch: <?php echo clean($batch['batch_number']); ?></span></td>
                                    <td style="color: var(--danger); font-weight: 700;">EXPIRED (<?php echo date('M d, Y', strtotime($batch['expiry_date'])); ?>)</td>
                                    <td><?php echo $batch['quantity']; ?></td>
                                </tr>
                                <?php endwhile; ?>

                                <!-- Show Expiring soon -->
                                <?php while ($batch = mysqli_fetch_assoc($expiring_result)): ?>
                                <tr>
                                    <td><strong><?php echo clean($batch['name']); ?></strong><br><span style="font-size: 0.8rem; color: var(--muted);">Batch: <?php echo clean($batch['batch_number']); ?></span></td>
                                    <td style="color: var(--warning); font-weight: 700;"><?php echo date('M d, Y', strtotime($batch['expiry_date'])); ?></td>
                                    <td><?php echo $batch['quantity']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--muted); text-align: center; padding: 20px;">No batches expiring in the next 30 days.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
            <!-- Form Card -->
            <div class="admin-card reveal-init">
                <div class="admin-card-header">
                    <h2>Log Expiry Batch</h2>
                </div>
                <form action="expiry-stock.php" method="POST" style="padding: 24px;">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="product_id" style="font-weight: 600;">Select Medicine</label>
                        <select name="product_id" id="product_id" class="form-control" required style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--border);">
                            <option value="">-- Choose Product --</option>
                            <?php while ($p = mysqli_fetch_assoc($products_list)): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo clean($p['name']); ?></option>
                            <?php endselect; ?>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="batch_number" style="font-weight: 600;">Batch Number</label>
                        <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="e.g. B-9988-A" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="expiry_date" style="font-weight: 600;">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label for="quantity" style="font-weight: 600;">Batch Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="e.g. 100" required>
                    </div>

                    <button type="submit" name="add_batch" class="btn btn-block"><i class="fas fa-plus"></i> Add Batch</button>
                </form>
            </div>

            <!-- Batches List -->
            <div class="admin-card reveal-init">
                <div class="admin-card-header">
                    <h2>Active Batches List</h2>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Batch No</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($all_batches_result) > 0): ?>
                            <?php while ($b = mysqli_fetch_assoc($all_batches_result)): 
                                $expired = strtotime($b['expiry_date']) < time();
                            ?>
                            <tr style="<?php if($expired) echo 'opacity: 0.6;'; ?>">
                                <td><strong><?php echo clean($b['name']); ?></strong></td>
                                <td><code><?php echo clean($b['batch_number']); ?></code></td>
                                <td>
                                    <span style="font-weight: 600; color: <?php echo $expired ? 'var(--danger)' : 'inherit'; ?>">
                                        <?php echo date('M d, Y', strtotime($b['expiry_date'])); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $b['quantity']; ?></strong></td>
                                <td style="font-size: 0.85rem; color: var(--muted);"><?php echo date('M d, Y', strtotime($b['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: var(--muted);">No batch files logged.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
