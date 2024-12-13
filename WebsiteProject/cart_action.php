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
$action = isset($_GET['action']) ? $_GET['action'] : '';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($action === 'add' && $product_id) {
    // Add product to cart or increase quantity if it already exists
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                            VALUES (?, ?, 1)
                            ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $_SESSION['message'] = "Product added to cart.";
} elseif ($action === 'remove' && $product_id) {
    // Decrease product quantity by 1
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();

    // If quantity reaches 0, remove the product from the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ? AND quantity <= 0");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $_SESSION['message'] = "One unit of the product removed from cart.";
} elseif ($action === 'remove_all' && $product_id) {
    // Remove all units of a specific product from the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $_SESSION['message'] = "All units of the product removed from cart.";
} elseif ($action === 'clear') {
    // Clear entire cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $_SESSION['message'] = "Cart cleared.";
}

$stmt->close();
$conn->close();

// Redirect back to the cart view
header("Location: cart_view.php");
exit;
