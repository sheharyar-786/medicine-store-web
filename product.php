<?php
$pageTitle = 'Product Details';
include 'includes/db_connect.php';
include 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: shop.php');
    exit();
}

$product_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM products WHERE id = '$product_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $pageTitle = 'Product Not Found';
    include 'includes/header.php';
    echo '<main class="container"><div class="empty-state"><h3>Product not found</h3><a href="shop.php" class="btn">Back to Shop</a></div></main>';
    include 'includes/footer.php';
    exit();
}

$product = mysqli_fetch_assoc($result);
$pageTitle = $product['name'];
include 'includes/header.php';
?>

<main class="container">
    <div class="product-detail">
        <div class="product-gallery reveal-init">
            <img src="assets/uploads/products/<?php echo clean($product['image_url']); ?>"
                 alt="<?php echo clean($product['name']); ?>"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22360%22 height=%22360%22%3E%3Crect fill=%22%23e3f2fd%22 width=%22360%22 height=%22360%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%230076bd%22 font-size=%2218%22%3ENo Image%3C/text%3E%3C/svg%3E'">
        </div>

        <div class="product-info reveal-init">
            <h1><?php echo clean($product['name']); ?></h1>
            <p class="product-meta">Generic: <?php echo clean($product['generic_name']); ?></p>
            <span class="category-badge"><?php echo clean($product['category']); ?></span>

            <p class="product-price"><?php echo formatPrice($product['price']); ?></p>

            <?php if (!empty($product['description'])): ?>
            <div class="product-description">
                <h3>Description</h3>
                <p><?php echo nl2br(clean($product['description'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if ($product['requires_prescription']): ?>
            <div class="prescription-alert">
                <i class="fas fa-file-medical"></i>
                This medicine requires a valid prescription at checkout.
            </div>
            <?php endif; ?>

            <form action="actions/add_to_cart_action.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="name" value="<?php echo clean($product['name']); ?>">
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="image" value="<?php echo $product['image_url']; ?>">

                <div class="add-to-cart-row">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="qty-input">
                    <button type="submit" class="btn"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                </div>
                <p class="stock-info"><i class="fas fa-box"></i> In stock: <?php echo $product['stock_quantity']; ?> units</p>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
