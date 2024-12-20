<?php

// Include necessary files and database connection
include_once __DIR__ . '/../utility/config.php'; // Ensure database connection
include_once __DIR__ . '/../models/cart_model.php'; // Include the cart model

// Ensure the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize cart items and total price
$cart_items = $_SESSION['cart'];
$total_price = 0;

// Debugging: Check cart contents
if (empty($cart_items)) {
    echo 'Cart is empty.';
    // Optional: Redirect to an empty cart view or homepage
    // header("Location: " . BASE_URL . "/views/cart_empty_view.php");
    exit;
}

// Fetch product details from the database for the items in the cart
$cart_data = fetchCartItems($conn, $cart_items);

// Ensure cart details are properly retrieved
if ($cart_data) {
    $cart_details = $cart_data['cart_details'];
    $total_price = $cart_data['total_price'];
} else {
    // Handle case where fetching cart data fails
    $cart_details = [];
    $total_price = 0;
}

// Debugging: Output fetched cart details
// Uncomment to debug the cart content
// var_dump($cart_details);
// var_dump($total_price);

// Close the database connection
$conn->close();
