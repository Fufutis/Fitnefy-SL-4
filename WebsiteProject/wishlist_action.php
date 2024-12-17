<?php
session_start();
include("repeat/config.php");

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to modify the wishlist.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);

        // Delete the item from the wishlist
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param('ii', $user_id, $product_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item removed from wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
