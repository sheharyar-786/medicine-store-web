<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Admin Dashboard';
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/header.php';

$order_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE status='pending'"));
$product_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM products"));
$total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders"));
$delivered_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE status='delivered'"));
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <p>Welcome back! Here's an overview of your store.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card reveal-init">
                <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h4>Pending Orders</h4>
                    <h2><?php echo $order_count; ?></h2>
                </div>
            </div>
            <div class="stat-card reveal-init">
                <div class="stat-icon green"><i class="fas fa-pills"></i></div>
                <div class="stat-info">
                    <h4>Total Products</h4>
                    <h2><?php echo $product_count; ?></h2>
                </div>
            </div>
            <div class="stat-card reveal-init">
                <div class="stat-icon orange"><i class="fas fa-shopping-bag"></i></div>
                <div class="stat-info">
                    <h4>All Orders</h4>
                    <h2><?php echo $total_orders; ?></h2>
                </div>
            </div>
            <div class="stat-card reveal-init">
                <div class="stat-icon red"><i class="fas fa-truck"></i></div>
                <div class="stat-info">
                    <h4>Delivered</h4>
                    <h2><?php echo $delivered_count; ?></h2>
                </div>
            </div>
        </div>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>Quick Actions</h2>
            </div>
            <div style="padding: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="add-products.php" class="btn"><i class="fas fa-plus"></i> Add Product</a>
                <a href="view-orders.php" class="btn btn-outline"><i class="fas fa-clipboard-list"></i> View Orders</a>
                <a href="categories.php" class="btn btn-outline"><i class="fas fa-tags"></i> Categories</a>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
