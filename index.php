<?php
$pageTitle = 'Home';
$pageStyles = ['home'];
include 'includes/db_connect.php';
include 'includes/header.php';
?>

<section class="hero reveal-init">
    <div class="container">
        <h1>Genuine Medicines, Delivered Fast</h1>
        <p>Your trusted online pharmacy in Pakistan. 100% authentic products.</p>
        <div class="hero-btns">
            <a href="shop.php" class="btn">Shop Now <i class="fas fa-arrow-right"></i></a>
            <a href="checkout.php" class="btn btn-secondary">Upload Prescription <i class="fas fa-upload"></i></a>
        </div>
    </div>
</section>

<main class="container">

    <section class="services-row">
        <div class="service-card reveal-init">
            <i class="fas fa-shipping-fast"></i>
            <h4>Home Delivery</h4>
            <p>Fast delivery across Pakistan</p>
        </div>
        <div class="service-card reveal-init">
            <i class="fas fa-file-medical"></i>
            <h4>Prescription</h4>
            <p>Easy upload & verification</p>
        </div>
        <div class="service-card reveal-init">
            <i class="fas fa-pills"></i>
            <h4>100% Genuine</h4>
            <p>Direct from manufacturers</p>
        </div>
    </section>

    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <a href="shop.php?category=Baby Care" class="cat-card reveal-init">
            <i class="fas fa-baby"></i>
            <h3>Baby Care</h3>
        </a>
        <a href="shop.php?category=Cardiac" class="cat-card reveal-init">
            <i class="fas fa-heartbeat"></i>
            <h3>Cardiac</h3>
        </a>
        <a href="shop.php?category=Pain Relief" class="cat-card reveal-init">
            <i class="fas fa-capsules"></i>
            <h3>Pain Relief</h3>
        </a>
        <a href="shop.php?category=Infection" class="cat-card reveal-init">
            <i class="fas fa-virus"></i>
            <h3>Infection</h3>
        </a>
        <a href="shop.php?category=Immunity" class="cat-card reveal-init">
            <i class="fas fa-user-shield"></i>
            <h3>Immunity</h3>
        </a>
    </div>

    <h2 class="section-title">Featured Medicines</h2>
    <div class="product-grid">
        <?php
        $query = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0):
            while ($product = mysqli_fetch_assoc($result)):
        ?>
        <div class="product-card reveal-init">
            <?php if ($product['requires_prescription']): ?>
            <span class="prescription-tag">Rx</span>
            <?php endif; ?>

            <a href="product.php?id=<?php echo $product['id']; ?>">
                <img src="assets/uploads/products/<?php echo clean($product['image_url']); ?>"
                     alt="<?php echo clean($product['name']); ?>"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22%3E%3Crect fill=%22%23e3f2fd%22 width=%22140%22 height=%22140%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%230076bd%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E'">
            </a>

            <h3><a href="product.php?id=<?php echo $product['id']; ?>"><?php echo clean($product['name']); ?></a></h3>
            <p class="generic-name"><?php echo clean($product['generic_name']); ?></p>
            <p class="price-tag"><?php echo formatPrice($product['price']); ?></p>

            <form action="actions/add_to_cart_action.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="name" value="<?php echo clean($product['name']); ?>">
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="image" value="<?php echo $product['image_url']; ?>">
                <button type="submit" class="btn add-to-cart-btn"><i class="fas fa-cart-plus"></i> Add to Cart</button>
            </form>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <div class="empty-state" style="grid-column: 1 / -1;">
            <i class="fas fa-pills"></i>
            <h3>No medicines listed yet</h3>
            <p>Check back soon or contact the admin to add products.</p>
        </div>
        <?php endif; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
