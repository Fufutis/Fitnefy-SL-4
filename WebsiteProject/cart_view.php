<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch product details from session cart
$cart_items = $_SESSION['cart'];
$total_price = 0;

if (!empty($cart_items)) {
    // Simulate fetching product details from the database
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $stmt = $conn->prepare("SELECT id, name, price, photo_blob, description FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        $row['quantity'] = $cart_items[$product_id]['quantity'];
        $row['total'] = $row['price'] * $row['quantity'];
        $cart_items[$product_id] = $row;
        $total_price += $row['total'];
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">Your cart is empty.</div>
        <?php else: ?>
            <table class="table table-bordered">
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
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>"
                                    alt="Product Image" class="img-thumbnail" style="width: 100px; height: auto;">
                                <br>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($item['total'], 2)); ?></td>
                            <td>
                                <a href="cart_action.php?action=remove&product_id=<?php echo $item['id']; ?>"
                                    class="btn btn-danger btn-sm">Remove One</a>
                                <a href="cart_action.php?action=remove_all&product_id=<?php echo $item['id']; ?>"
                                    class="btn btn-warning btn-sm">Remove All</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center">
                <h3>Total Price: $<?php echo number_format($total_price, 2); ?></h3>
                <div>
                    <a href="cart_action.php?action=clear" class="btn btn-danger">Clear Cart</a>
                    <!-- Buy Now Button -->
                    <a href="checkout.php" class="btn btn-success">Buy Now</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>