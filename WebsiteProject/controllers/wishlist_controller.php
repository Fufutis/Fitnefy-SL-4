<?php
session_start();
include_once __DIR__ . '/../utility/config.php'; // Database configuration

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to modify the wishlist.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($action === 'remove' && $product_id) {
        // Remove from wishlist
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param('ii', $user_id, $product_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item removed from wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
        }
        $stmt->close();
    } elseif ($action === 'add' && $product_id) {
        // Add to wishlist
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $user_id, $product_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item added to wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item to wishlist.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action or product ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
