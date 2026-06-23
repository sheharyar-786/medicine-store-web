<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = '..';
$currentPage = basename($_SERVER['PHP_SELF']);
$pendingCount = 0;

if (isset($conn)) {
    $pendingResult = mysqli_query($conn, "SELECT id FROM orders WHERE status='pending'");
    if ($pendingResult) {
        $pendingCount = mysqli_num_rows($pendingResult);
    }
}
?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo $base; ?>/index.php" class="logo">
            <span class="logo-icon"><i class="fas fa-notes-medical"></i></span>
            Admin Panel
        </a>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="manage-products.php" class="<?php echo in_array($currentPage, ['manage-products.php', 'add-products.php']) ? 'active' : ''; ?>">
                <i class="fas fa-pills"></i> Products
            </a>
        </li>
        <li>
            <a href="categories.php" class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
        </li>
        <li>
            <a href="view-orders.php" class="<?php echo in_array($currentPage, ['view-orders.php', 'order-details.php']) ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i> Orders
                <?php if ($pendingCount > 0): ?>
                <span class="sidebar-badge"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="sidebar-divider"></li>
        <li>
            <a href="<?php echo $base; ?>/index.php"><i class="fas fa-store"></i> View Store</a>
        </li>
        <li>
            <a href="<?php echo $base; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</aside>
