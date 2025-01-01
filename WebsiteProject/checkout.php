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

// Ensure the cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty. Add items to proceed.";
    header("Location: cart_view.php");
    exit;
}

$cart_items = $_SESSION['cart'];
$total_price = 0;
$valid_products = [];

// Fetch valid product details from the database
if (!empty($cart_items)) {
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));

    $stmt = $conn->prepare("SELECT id, price, seller_id FROM products WHERE id IN ($placeholders)");
    if ($stmt === false) {
        error_log("Query preparation failed: " . $conn->error);
        die("Database query failed.");
    }

    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        if (isset($cart_items[$product_id])) {
            $row['quantity'] = $cart_items[$product_id]['quantity'];
            $row['total'] = $row['price'] * $row['quantity'];
            $valid_products[$product_id] = $row;
            $total_price += $row['total'];
        }
    }
    $stmt->close();
}

// Abort if no valid products found
if (empty($valid_products)) {
    $_SESSION['message'] = "No valid products found in your cart.";
    header("Location: cart_view.php");
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Insert a new order group
    $order_group_stmt = $conn->prepare("INSERT INTO order_groups (user_id, total_price, order_timestamp) VALUES (?, ?, NOW())");
    $order_group_stmt->bind_param('id', $user_id, $total_price);
    if (!$order_group_stmt->execute()) {
        throw new Exception("Failed to create order group: " . $order_group_stmt->error);
    }
    $order_group_id = $order_group_stmt->insert_id;
    $order_group_stmt->close();

    // Insert individual orders
    $order_stmt = $conn->prepare("INSERT INTO orders (order_group_id, user_id, product_id, quantity, total_price, order_timestamp) 
                                  VALUES (?, ?, ?, ?, ?, NOW())");
    foreach ($valid_products as $product) {
        $order_stmt->bind_param(
            'iiidi',
            $order_group_id,
            $user_id,
            $product['id'],
            $product['quantity'],
            $product['total']
        );
        if (!$order_stmt->execute()) {
            throw new Exception("Failed to insert order for Product ID {$product['id']}: " . $order_stmt->error);
        }
    }
    $order_stmt->close();

    // Clear the cart
    unset($_SESSION['cart']);

    // Commit transaction
    $conn->commit();

    // Log success and redirect to success page
    error_log("Checkout successful for Order Group ID {$order_group_id}");
    $_SESSION['order_group_id'] = $order_group_id; // Store for receipt generation
    header("Location: order_success.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    error_log("Checkout failed: " . $e->getMessage());
    $_SESSION['message'] = "Checkout failed: " . $e->getMessage();
    header("Location: cart_view.php");
    exit;
}

$conn->close();
