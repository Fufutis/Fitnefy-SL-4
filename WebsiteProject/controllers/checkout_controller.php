<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/checkout_model.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to complete the checkout process.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure the cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty. Add items to proceed.";
    header("Location: " . BASE_URL . "/views/cart_view.php");
    exit;
}

try {
    $order_group_id = processCheckout($conn, $user_id, $_SESSION['cart']);
    unset($_SESSION['cart']); // Clear cart

    // Store order group ID for success page
    $_SESSION['order_group_id'] = $order_group_id;

    // Redirect to success view
    header("Location: " . BASE_URL . "/views/order_success.php");
    exit;
} catch (Exception $e) {
    $_SESSION['message'] = "Checkout failed: " . $e->getMessage();
    header("Location: " . BASE_URL . "/views/cart_view.php");
    exit;
}
