<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your cart.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$response = ['success' => false, 'message' => 'Invalid action.'];

if ($action === 'add' && $product_id) {
    // Add product to cart or increase quantity if it already exists
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                            VALUES (?, ?, 1)
                            ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Product added to cart.'];
    } else {
        $response = ['success' => false, 'message' => 'Failed to add product to cart.'];
    }
    $stmt->close();
} elseif ($action === 'remove' && $product_id) {
    // Decrease product quantity by 1
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        // If quantity reaches 0, remove the product from the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ? AND quantity <= 0");
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
        $response = ['success' => true, 'message' => 'One unit of the product removed from cart.'];
    } else {
        $response = ['success' => false, 'message' => 'Failed to remove product from cart.'];
    }
    $stmt->close();
} elseif ($action === 'remove_all' && $product_id) {
    // Remove all units of a specific product from the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'All units of the product removed from cart.'];
    } else {
        $response = ['success' => false, 'message' => 'Failed to remove all units of the product from cart.'];
    }
    $stmt->close();
} elseif ($action === 'clear') {
    // Clear entire cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Cart cleared.'];
    } else {
        $response = ['success' => false, 'message' => 'Failed to clear the cart.'];
    }
    $stmt->close();
}

// Return the response as JSON
echo json_encode($response);
$conn->close();
exit;
