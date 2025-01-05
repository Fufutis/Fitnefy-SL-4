<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

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
    $stmt = $conn->prepare("SELECT id, name, price, photo_blob, description FROM products WHERE id IN ($placeholders)");
    if ($stmt === false) {
        error_log("Query preparation failed: " . $conn->error);
        die("Database query failed.");
    }

    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        if (isset($cart_items[$product_id])) {
            $row['quantity'] = $cart_items[$product_id]['quantity'];
            $row['total'] = $row['price'] * $row['quantity'];
            $display_cart[$product_id] = $row; // Add to display cart
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <script>
        function confirmPurchase() {
            if (confirm("Are you sure you want to place this order?")) {
                window.location.href = "checkout.php";
            }
        }
    </script>
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
                <table class="table table-bordered sheet2">
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
                            <tr>
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
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($item['total'], 2)); ?></td>
                                <td>
                                    <!-- Use your custom .btn-design or .btn-in-cards for styling -->
                                    <a href="cart_action.php?action=remove&product_id=<?php echo $item['id']; ?>"
                                        class="btn sheet btn-design   mb-1 ">
                                        Remove One
                                    </a>
                                    <br>
                                    <a href="cart_action.php?action=remove_all&product_id=<?php echo $item['id']; ?>"
                                        class="btn sheet btn-design ">
                                        Remove All
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center">
                    <h3>Total Price: $<?php echo number_format($total_price, 2); ?></h3>
                    <div>
                        <!-- Link your classes or keep it as is -->
                        <a href="cart_action.php?action=clear" class="btn btn-design me-2">Clear Cart</a>
                        <button onclick="confirmPurchase()" class="btn btn-design">Buy Now</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
</body>

</html>