<?php
session_start();
include("repeat/config.php");

// Because this file is *only* returning JSON (for AJAX calls):
header('Content-Type: application/json');

// Make sure the cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Grab the 'action' from GET or POST
$action = $_REQUEST['action'] ?? null;
$productId = $_REQUEST['product_id'] ?? null;

// Default response
$response = [
    'success' => false,
    'message' => 'Invalid action.'
];

// Optional: If you need the item price or something else, you might connect to DB or do more logic here

switch ($action) {

        // ---------------------------------------------------------------------
        // Example: Add item to cart
        // (You may have your own logic to handle quantity, price, etc.)
        // ---------------------------------------------------------------------
    case 'add':
        if ($productId) {
            // Let's assume you pass 'quantity' in the request or default to 1
            $quantity = $_REQUEST['quantity'] ?? 1;
            // If already in cart, increment quantity
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                // Example of storing some info in the cart session
                $_SESSION['cart'][$productId] = [
                    'id' => $productId,
                    'quantity' => $quantity,
                    // 'price' => some lookup
                    // 'name'  => some lookup
                ];
            }
            $response['success'] = true;
            $response['message'] = "Item added to cart.";
        } else {
            $response['message'] = "No productId specified for 'add'.";
        }
        break;

        // ---------------------------------------------------------------------
        // Remove exactly ONE quantity from a product in the cart
        // ---------------------------------------------------------------------
    case 'remove':
        if ($productId && isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity']--;

            // If quantity dropped to zero or below, remove the product entirely
            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
                // If we removed it entirely, the item has no quantity or total
                $response['new_quantity'] = 0;
                $response['new_total']    = 0;
            } else {
                // If it still exists, compute new item quantity/total
                // Fetch price from the database
                $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->bind_param('i', $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $price = $row['price'];
                    $_SESSION['cart'][$productId]['price'] = $price; // Cache it back in session
                } else {
                    $price = 0;
                }
                $stmt->close();

                $newQty = $_SESSION['cart'][$productId]['quantity'];

                $response['new_quantity'] = $newQty;
                $response['new_total']    = $price * $newQty;
            }

            // Recompute the entire cart total
            $cartTotal = 0;
            foreach ($_SESSION['cart'] as $pid => $itemData) {
                $price = $itemData['price'] ?? 0;
                $qty   = $itemData['quantity'] ?? 0;
                $cartTotal += $price * $qty;
            }
            $response['updated_total'] = $cartTotal;

            $response['success'] = true;
            $response['message'] = 'One item removed successfully.';
        } else {
            $response['message'] = 'Item not found in cart or no productId given.';
        }
        break;

    case 'remove_all':
        if ($productId && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);

            // Now recompute the entire cart total
            $cartTotal = 0;
            foreach ($_SESSION['cart'] as $pid => $itemData) {
                $price = $itemData['price'] ?? 0;
                $qty   = $itemData['quantity'] ?? 0;
                $cartTotal += $price * $qty;
            }
            $response['updated_total'] = $cartTotal;

            // If you want the front-end to remove the row completely, 
            // there's no need for new_quantity or new_total
            $response['success'] = true;
            $response['message'] = 'All quantities of this product removed.';
        } else {
            $response['message'] = 'Item not found in cart or no productId given.';
        }
        break;

        // ---------------------------------------------------------------------
        // Clear entire cart
        // ---------------------------------------------------------------------
    case 'clear':
        // Clear the cart and reset total
        $_SESSION['cart'] = [];
        $response['updated_total'] = 0; // Cart is now empty
        $response['success'] = true;
        $response['message'] = 'Your cart has been cleared.';
        break;

    default:
        // Default "invalid action" response
        break;
}

// Output the final JSON
echo json_encode($response);
exit;
