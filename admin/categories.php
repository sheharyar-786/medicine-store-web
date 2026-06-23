<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Categories';
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/header.php';
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Categories</h1>
            <p>Organize medicines by category</p>
        </div>

        <div class="admin-form-card reveal-init" style="margin-bottom: 32px;">
            <h2 style="font-size: 1.1rem; margin-bottom: 20px;">Add New Category</h2>
            <form action="category_action.php" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <input type="text" name="cat_name" class="form-control" placeholder="Category name" required style="flex: 1; min-width: 200px;">
                <button type="submit" name="add_cat" class="btn"><i class="fas fa-plus"></i> Add Category</button>
            </form>
        </div>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>Existing Categories</h2>
            </div>
            <div style="padding: 20px 24px;">
                <ul class="category-list">
                    <?php
                    $catResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
                    if ($catResult && mysqli_num_rows($catResult) > 0):
                        while ($cat = mysqli_fetch_assoc($catResult)):
                    ?>
                    <li>
                        <span><i class="fas fa-tag" style="color: var(--primary); margin-right: 8px;"></i> <?php echo clean($cat['category_name']); ?></span>
                        <a href="#" class="action-delete" style="font-size: 0.85rem;"><i class="fas fa-trash"></i> Delete</a>
                    </li>
                    <?php endwhile; else: ?>
                    <li style="justify-content: center; color: var(--muted);">No categories yet. Add one above or they will appear when you add products.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
