<?php
session_start();
include("repeat/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to add to your wishlist.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = intval($_GET['product_id']);

    // Check if the product is already in the wishlist
    $stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Product is already in your wishlist.']);
    } else {
        // Add to wishlist
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $user_id, $product_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added to wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist.']);
        }
    }

    $stmt->close();
    $conn->close();
}
