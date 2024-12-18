<?php
session_start();

// Include configuration and models
include_once __DIR__ . "/../utility/config.php";
include_once __DIR__ . "/../models/product_model.php";

// Retrieve session variables
$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'] ?? 0;

// View and filter settings
$view_type = $_GET['view'] ?? 'all_products';
$category = $_GET['category'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'recent';
$sort_order = $_GET['sort_order'] ?? 'desc';

$sold_items = [];
$products = [];

// Fetch data based on role and view
if ($role === 'seller' || $role === 'both') {
    if ($view_type === 'sold_items') {
        $sold_items = getSoldItems($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
    } elseif ($view_type === 'my_products') {
        $products = getProductsBySeller($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
    }
}

if ($view_type === 'all_products' && ($role === 'user' || $role === 'both')) {
    $products = getAllProducts($conn, $category, $sort_by, $sort_order)->fetch_all(MYSQLI_ASSOC);
}

// Close connection
$conn->close();

// Load the correct view
if ($role === 'user') {
    include_once __DIR__ . "/../views/user_dashboard.php";
} elseif ($role === 'seller') {
    include_once __DIR__ . "/../views/seller_dashboard.php";
} else {
    include_once __DIR__ . "/../views/both_dashboard.php";
}
