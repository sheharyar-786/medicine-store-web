<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Manage Products';
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/header.php';

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Inventory Management</h1>
            <p>View and manage all medicines in your store</p>
        </div>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>All Products</h2>
                <a href="add-products.php" class="btn btn-sm"><i class="fas fa-plus"></i> Add New Medicine</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Generic</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <img src="../assets/uploads/products/<?php echo clean($row['image_url']); ?>"
                                     class="product-thumb" alt=""
                                     onerror="this.style.display='none'">
                            </td>
                            <td><strong><?php echo clean($row['name']); ?></strong></td>
                            <td><?php echo clean($row['generic_name']); ?></td>
                            <td><?php echo formatPrice($row['price']); ?></td>
                            <td><?php echo $row['stock_quantity']; ?></td>
                            <td class="table-actions">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No products found. <a href="add-products.php">Add your first medicine</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
