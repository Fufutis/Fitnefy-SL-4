<?php include_once __DIR__ . '/partials/header.php'; ?>
<?php include_once __DIR__ . '/partials/navbar.php'; ?>
<div class="container mt-5">
    <h1>Edit Product</h1>
    <form action="<?php echo BASE_URL; ?>/controllers/update_product_controller.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="product_type" class="form-label">Product Type</label>
            <select name="product_type" id="product_type" class="form-select">
                <option value="e-book" <?php echo $product['product_type'] === 'e-book' ? 'selected' : ''; ?>>E-Book</option>
                <option value="software" <?php echo $product['product_type'] === 'software' ? 'selected' : ''; ?>>Software</option>
                <option value="template" <?php echo $product['product_type'] === 'template' ? 'selected' : ''; ?>>Template</option>
                <option value="digital artwork" <?php echo $product['product_type'] === 'digital artwork' ? 'selected' : ''; ?>>Digital Artwork</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Product Image (Optional)</label>
            <input type="file" name="photo" id="photo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
<?php include_once __DIR__ . '/partials/footer.php'; ?>