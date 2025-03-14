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

// Validate cart items against the database
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    $stmt = $conn->prepare("SELECT id FROM products WHERE id IN ($placeholders)");
    if ($stmt === false) {
        error_log("Query preparation failed: " . $conn->error);
        die("Database query failed.");
    }

    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $existing_ids = [];
    while ($row = $result->fetch_assoc()) {
        $existing_ids[] = $row['id'];
    }

    $stmt->close();

    // Remove items from the session cart that no longer exist in the database
    foreach ($product_ids as $product_id) {
        if (!in_array($product_id, $existing_ids)) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
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
                <div class="alert sheet bold">Your cart is empty.</div>
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
                                        alt="Product Image "
                                        class="img-thumbnail sheet"
                                        style="width: 100px; height: auto;">
                                    <br>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td>$<?php echo htmlspecialchars($item['price']); ?></td>

                                <!-- Quantity Input -->
                                <td>
                                    <input
                                        type="number"
                                        class=" quantity-input"
                                        style="width: 60px"
                                        data-product-id="<?php echo $item['id']; ?>"
                                        value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                        min="1">
                                </td>

                                <!-- Total Price -->
                                <td class="item-total">
                                    $<?php echo htmlspecialchars(number_format($item['total'], 2)); ?>
                                </td>
                                <td>
                                    <button onclick="removeOne(<?php echo $item['id']; ?>)"
                                        class="btn btn-design btn-sm mb-1 sheet">
                                        Remove One
                                    </button>
                                    <br>
                                    <button onclick="removeAll(<?php echo $item['id']; ?>)"
                                        class="btn btn-design btn-sm sheet">
                                        Remove ALL
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
                        <button onclick="clearCart()" class="btn btn-design">Clear Cart</button>
                        <button onclick="confirmPurchase()" class="btn btn-design">Buy Now</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Update total price when quantity changes
        $(document).on('input', '.quantity-input', function() {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(this).val());
            if (quantity < 1) {
                $(this).val(1); // Prevent invalid quantity
                return;
            }

            // Send AJAX request to update quantity
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'update_quantity',
                    product_id: productId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update item total price
                        $(`#cart-item-${productId} .item-total`).text(`$${response.item_total.toFixed(2)}`);

                        // Update total price
                        $('#total-price').text(`$${response.total_price.toFixed(2)}`);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the quantity.');
                }
            });
        });

        // Function to display alert messages
        function displayMessage(message, type) {
            const alertBox = `
                    <div class="alert fixed-alert" role="alert" ">
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
                        $('.cart-table').replaceWith('<div class="alert sheet">Your cart is empty.</div>');

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
                        location.reload(); // Reload the page to update the cart
                    } else {
                        displayMessage(response.message, 'danger');
                        location.reload(); // Reload the page to update the cart
                    }
                },
                error: function() {
                    displayMessage('An error occurred while removing the item.', 'danger');
                    location.reload(); // Reload the page to update the cart
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
                        location.reload(); // Reload the page to update the cart
                    } else {
                        location.reload(); // Reload the page to update the cart
                        displayMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    displayMessage('An error occurred while removing the item.', 'danger');
                    location.reload(); // Reload the page to update the cart
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