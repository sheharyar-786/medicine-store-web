<?php
if (!isset($base)) {
    $isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $base = $isAdminPage ? '..' : '.';
}
$isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
?>

<?php if (!$isAdminPage): ?>
<footer class="site-footer">
    <div class="footer-grid container">
        <div class="footer-brand">
            <a href="<?php echo $base; ?>/index.php" class="logo">
                <span class="logo-icon"><i class="fas fa-notes-medical"></i></span>
                HealthCare <span>Store</span>
            </a>
            <p>Your trusted online pharmacy in Pakistan. Genuine medicines delivered to your doorstep.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $base; ?>/shop.php">Shop Medicines</a></li>
                <li><a href="<?php echo $base; ?>/checkout.php">Upload Prescription</a></li>
                <li><a href="<?php echo $base; ?>/my-orders.php">Track Orders</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Categories</h4>
            <ul>
                <li><a href="<?php echo $base; ?>/shop.php?category=Baby Care">Baby Care</a></li>
                <li><a href="<?php echo $base; ?>/shop.php?category=Cardiac">Cardiac</a></li>
                <li><a href="<?php echo $base; ?>/shop.php?category=Pain Relief">Pain Relief</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contact</h4>
            <p><i class="fas fa-phone"></i> +92 300 1234567</p>
            <p><i class="fas fa-envelope"></i> support@healthcarestore.pk</p>
            <div class="socials">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> HealthCare Store. All rights reserved.</p>
    </div>
</footer>
<?php endif; ?>

<script src="<?php echo $base; ?>/assets/js/main.js"></script>
</body>
</html>
