<?php
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/product_model.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to access the store.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'];
$view_type = $_GET['view'] ?? 'all_products'; // Default view

// Sanitize view type
$allowed_views = ['all_products', 'my_products'];
$view_type = in_array($view_type, $allowed_views) ? $view_type : 'all_products';

// Fetch products based on view type
$products = [];
if ($view_type === 'my_products') {
    $products = getProductsBySeller($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
} elseif ($view_type === 'all_products') {
    $products = getAllProducts($conn)->fetch_all(MYSQLI_ASSOC);
}

// Close the database connection
$conn->close();

// Include the view
include_once __DIR__ . '/../views/seller_store_view.php';
