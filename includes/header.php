<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$base = basePath();

$cartCount = getCartCount();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$userRole = $_SESSION['role'] ?? '';
$pageTitle = $pageTitle ?? 'Online Medicine Store';
$pageStyles = $pageStyles ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo clean($pageTitle); ?> | HealthCare Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo assetUrl('assets/css/base.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('assets/css/layout.css'); ?>">
    <?php foreach ($pageStyles as $style): ?>
    <link rel="stylesheet" href="<?php echo assetUrl('assets/css/pages/' . $style . '.css'); ?>">
    <?php endforeach; ?>
    <?php if ($isAdminPage): ?>
    <link rel="stylesheet" href="<?php echo assetUrl('assets/css/admin.css'); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="<?php echo $isAdminPage ? 'admin-body' : 'store-body'; ?><?php echo !empty($pageStyles[0]) ? ' page-' . clean($pageStyles[0]) : ''; ?>">

<?php 
$isAuthPage = in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php']);
if (!$isAdminPage && !$isAuthPage): 
?>
<header class="site-header">
    <nav class="navbar" id="navbar">
        <a href="<?php echo pageUrl('index.php'); ?>" class="logo">
            <span class="logo-icon"><i class="fas fa-notes-medical"></i></span>
            HealthCare <span>Store</span>
        </a>

        <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-bar">
            <form action="<?php echo pageUrl('shop.php'); ?>" method="GET">
                <input type="text" name="search" placeholder="Search medicines, salts..." value="<?php echo isset($_GET['search']) ? clean($_GET['search']) : ''; ?>">
                <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?php echo pageUrl('index.php'); ?>" class="nav-link">Home</a></li>
            <li><a href="<?php echo pageUrl('shop.php'); ?>" class="nav-link">Medicines</a></li>
            <li>
                <a href="<?php echo pageUrl('cart.php'); ?>" class="nav-link cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                    <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'admin'): ?>
                <li><a href="<?php echo pageUrl('admin/index.php'); ?>" class="nav-link"><i class="fas fa-user-shield"></i> Admin Panel</a></li>
                <?php elseif ($userRole === 'pharmacist'): ?>
                <li><a href="<?php echo pageUrl('pharmacist/dashboard.php'); ?>" class="nav-link"><i class="fas fa-prescription-bottle-alt"></i> Pharmacist Hub</a></li>
                <?php elseif ($userRole === 'driver'): ?>
                <li><a href="<?php echo pageUrl('driver/dashboard.php'); ?>" class="nav-link"><i class="fas fa-motorcycle"></i> Driver Portal</a></li>
                <?php else: ?>
                <li><a href="<?php echo pageUrl('my-orders.php'); ?>" class="nav-link">My Orders</a></li>
                <?php endif; ?>
                <li class="nav-user">
                    <span class="user-greeting">Hi, <?php echo clean(explode(' ', $userName)[0]); ?></span>
                    <a href="<?php echo pageUrl('logout.php'); ?>" class="btn btn-sm btn-outline">Logout</a>
                </li>
            <?php else: ?>
                <li><a href="<?php echo pageUrl('login.php'); ?>" class="nav-link">Login</a></li>
                <li><a href="<?php echo pageUrl('register.php'); ?>" class="btn btn-sm">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<?php endif; ?>
