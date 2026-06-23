<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'View Orders';
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';
include '../includes/header.php';
restrictToAdmin();

$query = "SELECT orders.*, users.full_name FROM orders
          JOIN users ON orders.user_id = users.id
          ORDER BY orders.order_date DESC";
$result = mysqli_query($conn, $query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Customer Orders</h1>
            <p>Review and manage all customer orders</p>
        </div>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>All Orders</h2>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Prescription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><?php echo clean($row['full_name']); ?></td>
                            <td><?php echo formatPrice($row['total_amount']); ?></td>
                            <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td>
                                <?php if (!empty($row['prescription_path'])): ?>
                                <a href="../assets/uploads/prescriptions/<?php echo clean($row['prescription_path']); ?>" target="_blank"><i class="fas fa-file-medical"></i> View</a>
                                <?php else: ?>
                                <span style="color: var(--muted);">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="update_order_status.php" method="POST" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>Approve</option>
                                        <option value="rejected" <?php if($row['status']=='rejected') echo 'selected'; ?>>Reject</option>
                                        <option value="delivered" <?php if($row['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
