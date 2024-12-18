<?php

include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/cart_model.php';

// Ensure the cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];

// Fetch cart details and total price
$cart_data = fetchCartItems($conn, $cart_items);
$cart_details = $cart_data['cart_details'];
$total_price = $cart_data['total_price'];

$conn->close();
