<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/product_model.php';

// Ensure the user is logged in and has the "seller" or "both" role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'both')) {
    $_SESSION['message'] = "Access denied. Only sellers can view this page.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch sold items using the model
$sold_items_result = getSoldItemsForSeller($conn, $seller_id);
$sold_items = $sold_items_result->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Include the view
include_once __DIR__ . '/../views/seller_sold_items.php';
