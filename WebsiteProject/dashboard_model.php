<?php

include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}

// Get user information
$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'];

// View and filter settings
$view_type = isset($_GET['view']) && in_array($_GET['view'], ['sold_items', 'my_products', 'all_products'])
    ? $_GET['view']
    : ($role === 'user' || $role === 'both' ? 'all_products' : 'sold_items');
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'recent'; // Default: Recent
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc'; // Default: Descending

// Initialize data arrays
$sold_items = [];
$products = [];

// Fetch data based on view and role
if ($view_type === 'sold_items' && ($role === 'seller' || $role === 'both')) {
    $stmt = $conn->prepare("
SELECT 
    o.id AS order_id, 
    p.name AS product_name, 
    o.quantity, 
    o.total_price, 
    og.order_timestamp AS order_date 
FROM orders o
JOIN products p ON o.product_id = p.id
JOIN order_groups og ON o.order_group_id = og.id
WHERE p.seller_id = ?
ORDER BY og.order_timestamp DESC

    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sold_items[] = $row;
    }
    $stmt->close();
}

if ($view_type === 'my_products' && ($role === 'seller' || $role === 'both')) {
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products WHERE seller_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

if ($view_type === 'all_products' && ($role === 'user' || $role === 'both')) {
    $query = "SELECT id, name, description, price, product_type, photo_blob, upload_timestamp FROM products WHERE 1=1";
    if (!empty($category)) {
        $query .= " AND product_type = ?";
    }
    $query .= " ORDER BY " . ($sort_by === 'price' ? "price" : "upload_timestamp") . " " . ($sort_order === 'asc' ? "ASC" : "DESC");

    $stmt = $conn->prepare($query);
    if (!empty($category)) {
        $stmt->bind_param('s', $category);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
