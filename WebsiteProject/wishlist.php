<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to add to your wishlist.";
    header("Location: index.php");
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
    $_SESSION['message'] = "Product is already in your wishlist.";
} else {
    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $user_id, $product_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added to wishlist.";
    } else {
        $_SESSION['message'] = "Failed to add product to wishlist.";
    }
}

$stmt->close();
$conn->close();

header("Location: wishlist_view.php");
exit;
