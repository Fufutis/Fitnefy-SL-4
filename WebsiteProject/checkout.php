<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to complete the checkout process.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure the cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty. Add items to proceed.";
    header("Location: cart_view.php");
    exit;
}

// Fetch product details for the items in the cart
$cart_items = $_SESSION['cart'];
$total_price = 0;

$placeholders = implode(',', array_fill(0, count($cart_items), '?'));
$stmt = $conn->prepare("SELECT id, name, price, seller_id FROM products WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
$stmt->execute();
$result = $stmt->get_result();

// Calculate total price and prepare for order insertion
$products = [];
while ($row = $result->fetch_assoc()) {
    $product_id = $row['id'];
    $row['quantity'] = $cart_items[$product_id]['quantity'];
    $row['total'] = $row['quantity'] * $row['price'];
    $products[] = $row;
    $total_price += $row['total'];
}
$stmt->close();

// Begin the transaction
$conn->begin_transaction();

try {
    // Insert a new order group
    $order_group_stmt = $conn->prepare("INSERT INTO order_groups (user_id, created_at) VALUES (?, NOW())");
    $order_group_stmt->bind_param('i', $user_id);
    if (!$order_group_stmt->execute()) {
        throw new Exception("Failed to create order group: " . $conn->error);
    }
    $order_group_id = $order_group_stmt->insert_id;
    $order_group_stmt->close();

    // Insert individual orders for each product
    $order_stmt = $conn->prepare("INSERT INTO orders (order_group_id, user_id, product_id, quantity, total_price, seller_id) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $product_id = $product['id'];
        $quantity = $product['quantity'];
        $price = $product['price'];
        $seller_id = $product['seller_id'];
        $total = $product['total'];

        $order_stmt->bind_param('iiiiii', $order_group_id, $user_id, $product_id, $quantity, $total, $seller_id);
        if (!$order_stmt->execute()) {
            throw new Exception("Failed to insert order: " . $order_stmt->error);
        }
    }
    $order_stmt->close();

    // Clear the cart after successful checkout
    unset($_SESSION['cart']);

    // Commit the transaction
    $conn->commit();

    // Redirect to order success page
    $_SESSION['order_group_id'] = $order_group_id; // Store order group ID for receipt
    header("Location: order_success.php");
    exit;
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    $_SESSION['message'] = "Checkout failed: " . $e->getMessage();
    header("Location: cart_view.php");
    exit;
}

$conn->close();
