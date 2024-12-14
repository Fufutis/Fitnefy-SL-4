<?php
session_start();
// Ensure the user is logged in and has seller or both role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'both')) {
    header("Location: index.php");
    exit;
}

include("repeat/header.php");
include("repeat/navbar.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Add Product</h2>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
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
</body>

</html>