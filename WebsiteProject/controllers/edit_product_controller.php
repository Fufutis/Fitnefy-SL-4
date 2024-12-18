<?php
session_start();
include_once __DIR__ . '/../utility/config.php';

// Ensure the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'seller') {
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Check if a product ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "No product selected for editing.";
    header("Location: " . BASE_URL . "/views/seller_store.php");
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
    header("Location: " . BASE_URL . "/views/seller_store.php");
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Pass the product to the view
include_once __DIR__ . '/../views/seller_edit_product.php';
