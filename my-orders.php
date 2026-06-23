<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'My Orders';
$pageStyles = ['orders'];
include 'includes/db_connect.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<main class="container orders-page">
    <div class="page-header">
        <h1>My Order History</h1>
        <p>Track and review your past orders</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="cart-table-wrap reveal-init">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo formatPrice($row['total_amount']); ?></td>
                    <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state reveal-init">
        <i class="fas fa-clipboard-list"></i>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Start shopping to see your order history here.</p>
        <a href="shop.php" class="btn" style="margin-top: 20px;"><i class="fas fa-pills"></i> Browse Medicines</a>
    </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
