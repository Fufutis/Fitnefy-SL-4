<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your cart.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items with detailed product info
$stmt = $conn->prepare("
    SELECT 
        c.product_id, 
        p.name, 
        p.description, 
        p.price, 
        c.quantity, 
        p.photo_blob, 
        (c.quantity * p.price) AS total 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $total_price += $row['total'];
    $cart_items[] = $row;
}

$stmt->close();
$conn->close();
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
                                <a href="cart_action.php?action=remove&product_id=<?php echo $item['product_id']; ?>"
                                    class="btn btn-danger btn-sm">Remove One</a>
                                <a href="cart_action.php?action=remove_all&product_id=<?php echo $item['product_id']; ?>"
                                    class="btn btn-warning btn-sm">Remove All</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center">
                <h3>Total Price: $<?php echo number_format($total_price, 2); ?></h3>
                <a href="cart_action.php?action=clear" class="btn btn-danger">Clear Cart</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>