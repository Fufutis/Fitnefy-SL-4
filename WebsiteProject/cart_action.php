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
            $response['message'] = "Item (ID: $productId) added to cart.";
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

            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
            $response['success'] = true;
            $response['message'] = 'One item removed successfully.';
        } else {
            $response['message'] = 'Item not found in cart or no productId given.';
        }
        break;

        // ---------------------------------------------------------------------
        // Remove ALL quantity of a product
        // ---------------------------------------------------------------------
    case 'remove_all':
        if ($productId && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
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
        $_SESSION['cart'] = [];
        $response['success'] = true;
        $response['message'] = 'Your cart has been cleared.';
        break;

        // ---------------------------------------------------------------------
        // If none of the above cases matched, we keep "Invalid action."
        // ---------------------------------------------------------------------
    default:
        // We do nothing extra here.
        break;
}

// (Optional) If you need to return updated totals in each response, 
// you'd compute them here and add to $response. E.g.:
// $response['new_total']     = calculateNewTotal();
// $response['updated_total'] = calculateCartTotal(); 
// etc.

// Output the final JSON
echo json_encode($response);
exit;
