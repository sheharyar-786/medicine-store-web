<?php
require_once __DIR__ . '/config.php';

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
        <a href="<?php echo pageUrl('index.php'); ?>" class="logo">
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
        <li>
            <a href="sales-reports.php" class="<?php echo $currentPage === 'sales-reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Sales Reports
            </a>
        </li>
        <li>
            <a href="expiry-stock.php" class="<?php echo $currentPage === 'expiry-stock.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Expiry & Stock
            </a>
        </li>
        <li>
            <a href="refill-reminders.php" class="<?php echo $currentPage === 'refill-reminders.php' ? 'active' : ''; ?>">
                <i class="fas fa-bell"></i> Refill Reminders
            </a>
        </li>
        <li>
            <a href="manage-staff.php" class="<?php echo $currentPage === 'manage-staff.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Manage Staff
            </a>
        </li>
        <li class="sidebar-divider"></li>
        <li>
            <a href="<?php echo pageUrl('index.php'); ?>"><i class="fas fa-store"></i> View Store</a>
        </li>
        <li>
            <a href="<?php echo pageUrl('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</aside>
