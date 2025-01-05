<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// If your app requires a user to be logged in to view cart:
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}

// OPTIONAL: If you need role-based checks (like in your dashboard)
$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'] ?? null;

// Ensure the cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];
$total_price = 0;

// Create a display-friendly cart
$display_cart = [];

if (!empty($cart_items)) {
    // Prepare SQL placeholders
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));

    // Fetch product details from the database
    $stmt = $conn->prepare("
        SELECT 
            id, 
            name, 
            price, 
            photo_blob, 
            description
        FROM products 
        WHERE id IN ($placeholders)
    ");

    if ($stmt === false) {
        error_log("Query preparation failed: " . $conn->error);
        die("Database query failed.");
    }

    // The keys of $cart_items are the product IDs
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        if (isset($cart_items[$product_id])) {
            $row['quantity'] = $cart_items[$product_id]['quantity'];
            $row['total'] = $row['price'] * $row['quantity'];
            $display_cart[$product_id] = $row;
            $total_price += $row['total'];
        } else {
            error_log("Product ID {$product_id} is missing from the session cart.");
        }
    }

    // Log missing products from the database
    foreach (array_keys($cart_items) as $product_id) {
        if (!isset($display_cart[$product_id])) {
            error_log("Product ID {$product_id} is missing from the database.");
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <!-- Include your CSS files here -->
</head>

<body>
    <!-- Tiled Background -->
    <div class="bg-container">
        <?php for ($i = 0; $i < 3000; $i++): ?>
            <div class="tile"></div>
        <?php endfor; ?>
    </div>

    <div class="main-content">
        <div class="container mt-5 background">
            <h1 class="mb-4">Shopping Cart</h1>

            <?php if (empty($display_cart)): ?>
                <div class="alert alert-info">Your cart is empty.</div>
            <?php else: ?>
                <!-- ADD a .cart-table class here so we can target it in JS -->
                <table class="table table-bordered sheet2 cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($display_cart as $item): ?>
                            <!-- ADD an id to this <tr> so we can target it by #cart-item-ID -->
                            <tr id="cart-item-<?php echo $item['id']; ?>">
                                <td>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>"
                                        alt="Product Image"
                                        class="img-thumbnail"
                                        style="width: 100px; height: auto;">
                                    <br>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td>$<?php echo htmlspecialchars($item['price']); ?></td>

                                <!-- ADD a .quantity class so we can update it with jQuery -->
                                <td class="quantity"><?php echo htmlspecialchars($item['quantity']); ?></td>

                                <!-- ADD a .total class so we can update it with jQuery -->
                                <td class="total">$<?php echo htmlspecialchars(number_format($item['total'], 2)); ?></td>
                                <td>
                                    <button onclick="removeOne(<?php echo $item['id']; ?>)"
                                        class="btn btn-design btn-sm mb-1">
                                        Remove One
                                    </button>
                                    <br>
                                    <button onclick="removeAll(<?php echo $item['id']; ?>)"
                                        class="btn btn-design btn-sm">
                                        Remove All
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Wrap total price in an element with id="total-price" -->
                <div class="d-flex justify-content-between align-items-center">
                    <h3>
                        Total Price:
                        <span id="total-price">
                            $<?php echo number_format($total_price, 2); ?>
                        </span>
                    </h3>
                    <div>
                        <button onclick="clearCart()(<?php echo $item['id']; ?>)" class="btn btn-design">Clear Cart</button>
                        <button onclick="confirmPurchase()" class="btn btn-design">Buy Now</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Function to display alert messages
        function displayMessage(message, type) {
            const alertBox = `
                <div class="alert alert-${type} fixed-alert" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 90%; max-width: 500px; text-align: center;">
                    ${message}
                </div>`;
            document.body.insertAdjacentHTML('beforeend', alertBox);
            setTimeout(() => {
                const alert = document.querySelector('.fixed-alert');
                if (alert) alert.remove();
            }, 3000);
        }

        function clearCart() {
            if (!confirm("Are you sure you want to clear the cart?")) return;
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'clear'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMessage(response.message, 'success');

                        // Remove all rows from the table
                        $('tbody').empty();

                        // Show the empty cart message
                        $('.cart-table').replaceWith('<div class="alert alert-info">Your cart is empty.</div>');

                        // Reset total price
                        $('#total-price').text('$0.00');
                    } else {
                        displayMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    displayMessage('An error occurred while clearing the cart.', 'danger');
                }
            });
        }

        // Function to remove one quantity of a product
        function removeOne(productId) {
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'remove',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMessage(response.message, 'success');

                        // Update the quantity and total in the table
                        const row = $('#cart-item-' + productId);
                        row.find('.quantity').text(response.new_quantity);
                        row.find('.total').text('$' + response.new_total.toFixed(2));

                        // Update the overall total price
                        $('#total-price').text('$' + response.updated_total.toFixed(2));

                        // If quantity is 0, remove the row
                        if (response.new_quantity === 0) {
                            row.remove();

                            // If cart is empty, show the empty cart message
                            if ($('tbody tr').length === 0) {
                                $('.cart-table').replaceWith('<div class="alert alert-info">Your cart is empty.</div>');
                            }
                        }
                    } else {
                        displayMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    displayMessage('An error occurred while removing the item.', 'danger');
                }
            });
        }

        // Function to remove all quantities of a product
        function removeAll(productId) {
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'remove_all',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMessage(response.message, 'success');

                        // Remove the entire row
                        $('#cart-item-' + productId).remove();

                        // Update the overall total price
                        $('#total-price').text('$' + response.updated_total.toFixed(2));

                        // If cart is empty, show the empty cart message
                        if ($('tbody tr').length === 0) {
                            $('.cart-table').replaceWith('<div class="alert alert-info">Your cart is empty.</div>');
                        }
                    } else {
                        displayMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    displayMessage('An error occurred while removing the item.', 'danger');
                }
            });
        }

        // Function to confirm purchase
        function confirmPurchase() {
            if (confirm("Are you sure you want to place this order?")) {
                window.location.href = "checkout.php";
            }
        }
    </script>
</body>

</html>