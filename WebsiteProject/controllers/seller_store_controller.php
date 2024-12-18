<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/product_model.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "Please log in to view your store.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'];
$view_type = $_GET['view'] ?? 'all_products';

// Restrict views based on roles
if ($role === 'user') {
    $view_type = 'all_products';
} elseif ($role === 'seller') {
    $view_type = 'my_products';
} elseif ($role === 'both' && !in_array($view_type, ['all_products', 'my_products'])) {
    $view_type = 'all_products';
}

// Fetch products based on view type
if ($view_type === 'my_products') {
    $products = getProductsBySeller($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
} else {
    $products = getAllProducts($conn)->fetch_all(MYSQLI_ASSOC);
}

$conn->close();

// Load the view
include_once __DIR__ . '/../views/seller_store_view.php';
