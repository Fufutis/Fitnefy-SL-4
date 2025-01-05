<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");
// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['seller', 'both'])) {
    $_SESSION['message'] = "You must be a seller to edit products.";
    header("Location: index.php");
    exit;
}

// Check if a product ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "No product selected for editing.";
    header("Location: seller_store.php");
    exit;
}

$product_id = intval($_GET['id']);
$seller_id = $_SESSION['user_id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param('ii', $product_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Product not found or you do not have permission to edit it.";
    header("Location: seller_store.php");
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>

<body>
    <div class="bg-container">
        <?php

        for ($i = 0; $i < 3000; $i++) {
            echo '<div class="tile"></div>';
        }
        ?>
    </div>
    <div class="main-content">
        <div class="container mt-5 background">
            <h1 class="mb-4">Edit Product</h1>
            <form action="update_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="product_type" class="form-label">Product Type</label>
                    <select name="product_type" id="product_type" class="form-select" required>
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
                <button type="submit" class="btn bg-item">Update Product</button>
            </form>
        </div>
</body>

</html>