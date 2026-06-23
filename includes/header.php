<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$base = $isAdminPage ? '..' : '.';

if (!function_exists('getCartCount')) {
    include_once __DIR__ . '/functions.php';
}

$cartCount = getCartCount();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$userRole = $_SESSION['role'] ?? '';
$pageTitle = $pageTitle ?? 'Online Medicine Store';
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
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
    <?php if ($isAdminPage): ?>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/admin.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="<?php echo $isAdminPage ? 'admin-body' : 'store-body'; ?>">

<?php if (!$isAdminPage): ?>
<header class="site-header">
    <nav class="navbar" id="navbar">
        <a href="<?php echo $base; ?>/index.php" class="logo">
            <span class="logo-icon"><i class="fas fa-notes-medical"></i></span>
            HealthCare <span>Store</span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-bar">
            <form action="<?php echo $base; ?>/shop.php" method="GET">
                <input type="text" name="search" placeholder="Search medicines, salts..." value="<?php echo isset($_GET['search']) ? clean($_GET['search']) : ''; ?>">
                <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?php echo $base; ?>/index.php" class="nav-link">Home</a></li>
            <li><a href="<?php echo $base; ?>/shop.php" class="nav-link">Medicines</a></li>
            <li>
                <a href="<?php echo $base; ?>/cart.php" class="nav-link cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                    <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'admin'): ?>
                <li><a href="<?php echo $base; ?>/admin/index.php" class="nav-link"><i class="fas fa-user-shield"></i> Admin</a></li>
                <?php else: ?>
                <li><a href="<?php echo $base; ?>/my-orders.php" class="nav-link">My Orders</a></li>
                <?php endif; ?>
                <li class="nav-user">
                    <span class="user-greeting">Hi, <?php echo clean(explode(' ', $userName)[0]); ?></span>
                    <a href="<?php echo $base; ?>/logout.php" class="btn btn-sm btn-outline">Logout</a>
                </li>
            <?php else: ?>
                <li><a href="<?php echo $base; ?>/login.php" class="nav-link">Login</a></li>
                <li><a href="<?php echo $base; ?>/register.php" class="btn btn-sm">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<?php endif; ?>
