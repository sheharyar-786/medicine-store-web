<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Order Details';
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: view-orders.php');
    exit();
}

$order_id = (int) $_GET['id'];
$query = "SELECT orders.*, users.full_name, users.phone, users.address
          FROM orders JOIN users ON orders.user_id = users.id
          WHERE orders.id = $order_id";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: view-orders.php');
    exit();
}
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Order #<?php echo $order['id']; ?></h1>
            <p>Placed on <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
        </div>

        <div class="order-detail-grid reveal-init">
            <div class="detail-card">
                <h3><i class="fas fa-user"></i> Customer Info</h3>
                <p><strong>Name:</strong> <?php echo clean($order['full_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo clean($order['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo clean($order['address']); ?></p>
                <p><strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
            </div>

            <div class="detail-card">
                <h3><i class="fas fa-file-medical"></i> Prescription</h3>
                <?php if (!empty($order['prescription_path'])): ?>
                <img src="../assets/uploads/prescriptions/<?php echo clean($order['prescription_path']); ?>" class="prescription-preview" alt="Prescription">
                <?php else: ?>
                <p style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> No prescription uploaded.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-form-card reveal-init">
            <form action="update_order_status.php" method="POST" class="status-form">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <label for="status" style="font-weight: 600; margin-right: 12px;">Update Status:</label>
                <select id="status" name="status" class="status-select">
                    <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if($order['status']=='approved') echo 'selected'; ?>>Approve & Dispense</option>
                    <option value="rejected" <?php if($order['status']=='rejected') echo 'selected'; ?>>Reject Order</option>
                    <option value="delivered" <?php if($order['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                </select>
                <button type="submit" class="btn"><i class="fas fa-check"></i> Update Status</button>
                <a href="view-orders.php" class="btn btn-outline" style="margin-left: 8px;">Back to Orders</a>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
