<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Add Product';
include '../includes/functions.php';
include '../includes/header.php';
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Add New Medicine</h1>
            <p>Fill in the details to add a product to your inventory</p>
        </div>

        <div class="admin-form-card reveal-init">
            <form action="add_product_action.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Medicine Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Panadol" required>
                    </div>
                    <div class="form-group">
                        <label for="generic_name">Generic Name</label>
                        <input type="text" id="generic_name" name="generic_name" class="form-control" placeholder="e.g. Paracetamol">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="General">General</option>
                            <option value="Cardiac">Cardiac</option>
                            <option value="Baby Care">Baby Care</option>
                            <option value="Pain Relief">Pain Relief</option>
                            <option value="Infection">Infection</option>
                            <option value="Antibiotics">Antibiotics</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (Rs.)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" class="form-control" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <img id="preview-img" alt="Preview">
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="requires_prescription" name="requires_prescription" value="1">
                    <label for="requires_prescription">Requires Prescription</label>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Medicine description and usage info"></textarea>
                </div>

                <button type="submit" name="submit_product" class="btn"><i class="fas fa-save"></i> Save Medicine</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
