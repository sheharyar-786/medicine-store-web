<?php
$pageTitle = 'Shop Medicines';
include 'includes/db_connect.php';
include 'includes/functions.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

$sql = "SELECT * FROM products WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR generic_name LIKE '%$search%')";
}
if (!empty($category)) {
    $sql .= " AND category = '$category'";
}
$result = mysqli_query($conn, $sql);
?>

<main class="container">
    <div class="page-header">
        <h1>Browse Medicines</h1>
        <p><?php echo !empty($category) ? 'Showing results in ' . clean($category) : 'Find genuine medicines at the best prices'; ?></p>
    </div>

    <div class="shop-toolbar reveal-init">
        <h2><?php echo mysqli_num_rows($result); ?> products found</h2>
        <form action="shop.php" method="GET">
            <?php if (!empty($search)): ?>
            <input type="hidden" name="search" value="<?php echo clean($search); ?>">
            <?php endif; ?>
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="General" <?php if($category == 'General') echo 'selected'; ?>>General</option>
                <option value="Cardiac" <?php if($category == 'Cardiac') echo 'selected'; ?>>Cardiac</option>
                <option value="Baby Care" <?php if($category == 'Baby Care') echo 'selected'; ?>>Baby Care</option>
                <option value="Pain Relief" <?php if($category == 'Pain Relief') echo 'selected'; ?>>Pain Relief</option>
                <option value="Infection" <?php if($category == 'Infection') echo 'selected'; ?>>Infection</option>
                <option value="Immunity" <?php if($category == 'Immunity') echo 'selected'; ?>>Immunity</option>
                <option value="Antibiotics" <?php if($category == 'Antibiotics') echo 'selected'; ?>>Antibiotics</option>
            </select>
        </form>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="product-card reveal-init">
            <?php if ($row['requires_prescription']): ?>
            <span class="prescription-tag">Rx Required</span>
            <?php endif; ?>

            <a href="product.php?id=<?php echo $row['id']; ?>">
                <img src="assets/uploads/products/<?php echo clean($row['image_url']); ?>"
                     alt="<?php echo clean($row['name']); ?>"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22%3E%3Crect fill=%22%23e3f2fd%22 width=%22140%22 height=%22140%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%230076bd%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E'">
            </a>

            <h3><a href="product.php?id=<?php echo $row['id']; ?>"><?php echo clean($row['name']); ?></a></h3>
            <p class="generic-name"><?php echo clean($row['generic_name']); ?></p>
            <p class="price-tag"><?php echo formatPrice($row['price']); ?></p>

            <form action="actions/add_to_cart_action.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="name" value="<?php echo clean($row['name']); ?>">
                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                <input type="hidden" name="image" value="<?php echo $row['image_url']; ?>">
                <button type="submit" class="btn add-to-cart-btn"><i class="fas fa-cart-plus"></i> Add to Cart</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty-state reveal-init">
        <i class="fas fa-search"></i>
        <h3>No medicines found</h3>
        <p>Try adjusting your search or browse all categories.</p>
        <a href="shop.php" class="btn" style="margin-top: 20px;">View All Medicines</a>
    </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
