<?php
session_start();
include("repeat/config.php");

// Ensure cart is not empty
if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty.";
    header("Location: cart_view.php");
    exit;
}

// Simulate saving the order to the database (adjust this according to your schema)
$order_id = rand(1000, 9999); // Example order ID generation

// Loop through the cart and save items (in production, you should use SQL)
$cart_items = $_SESSION['cart'];
foreach ($cart_items as $product_id => $details) {
    // Save product_id, quantity, etc. to the orders table
}

// Clear the cart after the purchase
unset($_SESSION['cart']);

// Redirect to an order confirmation page
header("Location: order_success.php?order_id=$order_id");
exit;
