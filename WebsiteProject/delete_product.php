<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to delete a product.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

// Validate that the product belongs to the logged-in seller
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param('ii', $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product belongs to the seller, proceed to delete
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the product.";
    }
} else {
    // Product does not belong to the logged-in seller
    $_SESSION['message'] = "Unauthorized action.";
}

$stmt->close();
$conn->close();

header("Location: seller_store.php?view=my_products");
exit;
