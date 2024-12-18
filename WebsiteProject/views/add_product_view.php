<?php include_once __DIR__ . '/partials/header.php'; ?>
<?php include_once __DIR__ . '/partials/navbar.php'; ?>

<div class="container mt-5">
    <h2>Add Product</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php
            echo htmlspecialchars($_SESSION['message']);
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/controllers/submit_product_controller.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price ($)</label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label for="product_type" class="form-label">Product Type</label>
            <select id="product_type" name="product_type" class="form-select" required>
                <option value="e-book">E-Book</option>
                <option value="software">Software</option>
                <option value="template">Template</option>
                <option value="digital artwork">Digital Artwork</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>
</div>

<?php include_once __DIR__ . '/partials/footer.php'; ?>