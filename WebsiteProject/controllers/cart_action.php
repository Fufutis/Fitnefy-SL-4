<?php
session_start();
include_once __DIR__ . '/../utility/config.php'; // Ensure DB connection exists

// Ensure the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Validate input
$action = isset($_GET['action']) ? $_GET['action'] : '';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$response = ['success' => false, 'message' => 'Invalid action.'];

if ($action === 'add' && $product_id) {
    // Add product to cart or increase quantity if it already exists
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity' => 1,
        ];
    }
    $response = ['success' => true, 'message' => 'Product added to cart.'];
} elseif ($action === 'remove' && $product_id) {
    // Decrease product quantity by 1
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] -= 1;
        if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$product_id]); // Remove product if quantity is 0
        }
        $response = ['success' => true, 'message' => 'One unit of the product removed from cart.'];
    } else {
        $response = ['success' => false, 'message' => 'Product not found in cart.'];
    }
} elseif ($action === 'remove_all' && $product_id) {
    // Remove all units of a specific product
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $response = ['success' => true, 'message' => 'All units of the product removed from cart.'];
    } else {
        $response = ['success' => false, 'message' => 'Product not found in cart.'];
    }
} elseif ($action === 'clear') {
    // Clear entire cart
    $_SESSION['cart'] = [];
    $response = ['success' => true, 'message' => 'Cart cleared.'];
}

// Return the response as JSON
echo json_encode($response);
exit;
