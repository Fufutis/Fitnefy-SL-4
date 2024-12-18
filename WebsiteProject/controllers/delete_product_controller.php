<?php
session_start();
include_once __DIR__ . '/../utility/config.php'; // DB connection
include_once __DIR__ . '/../models/product_model.php'; // Product model functions

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to delete a product.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id > 0) {
    // Validate ownership and delete product
    if (productBelongsToSeller($conn, $product_id, $user_id)) {
        if (deleteProduct($conn, $product_id)) {
            $_SESSION['message'] = "Product deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete the product.";
        }
    } else {
        $_SESSION['message'] = "Unauthorized action.";
    }
} else {
    $_SESSION['message'] = "Invalid product ID.";
}

// Redirect to the seller's product page
header("Location: " . BASE_URL . "/views/seller_store.php?view=my_products");
exit;
