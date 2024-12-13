<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to manage your cart.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'add') {
    // Add product to cart
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added to cart.";
    } else {
        $_SESSION['message'] = "Failed to add product to cart.";
    }
} elseif ($action === 'remove') {
    // Remove product from cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product removed from cart.";
    }
}

$stmt->close();
$conn->close();

header("Location: cart_view.php");
exit;
