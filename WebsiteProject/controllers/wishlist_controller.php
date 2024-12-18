<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/wishlist_model.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to modify the wishlist.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($product_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    // Handle remove action
    if ($action === 'remove') {
        $success = removeWishlistItem($conn, $user_id, $product_id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Item removed from wishlist.' : 'Failed to remove item.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
