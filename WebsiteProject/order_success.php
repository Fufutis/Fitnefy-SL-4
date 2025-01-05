<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your order details.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the order group ID exists
if (!isset($_SESSION['order_group_id'])) {
    $_SESSION['message'] = "No order found. Please try again.";
    header("Location: cart_view.php");
    exit;
}

$order_group_id = $_SESSION['order_group_id'];

// Fetch order group details
$order_group_stmt = $conn->prepare("
    SELECT og.id AS order_group_id, og.order_timestamp, 
           SUM(o.total_price) AS total_price
    FROM order_groups og
    JOIN orders o ON og.id = o.order_group_id
    WHERE og.id = ? AND og.user_id = ?
    GROUP BY og.id, og.order_timestamp
");

$order_group_stmt->bind_param('ii', $order_group_id, $user_id);
$order_group_stmt->execute();
$order_group_result = $order_group_stmt->get_result();

if ($order_group_result->num_rows === 0) {
    $_SESSION['message'] = "No order found.";
    header("Location: cart_view.php");
    exit;
}

$order_group = $order_group_result->fetch_assoc();
$order_group_stmt->close();

// Fetch individual product details from the order
$order_items_stmt = $conn->prepare("
    SELECT o.quantity, o.total_price, 
           p.name, p.price, p.photo_blob
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.order_group_id = ?
");
$order_items_stmt->bind_param('i', $order_group_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();

$order_items = [];
while ($row = $order_items_result->fetch_assoc()) {
    $order_items[] = $row;
}

$order_items_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>

</head>

<body>
    <div class="bg-container">
        <?php for ($i = 0; $i < 3000; $i++): ?>
            <div class="tile"></div>
        <?php endfor; ?>
    </div>
    <div class="main-content">
        <div class="container mt-5">

            <h1>Order Confirmation</h1>

            <!-- Order Summary -->
            <div class="alert alert-success">
                <strong>Order ID:</strong> <?php echo htmlspecialchars($order_group['order_group_id']); ?><br>
                <strong>Date:</strong> <?php echo htmlspecialchars($order_group['order_timestamp']); ?><br>
                <strong>Total Price:</strong> $<?php echo number_format($order_group['total_price'], 2); ?>
            </div>

            <!-- Ordered Items -->
            <?php if (empty($order_items)): ?>
                <div class="alert alert-info">No items found in this order.</div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead class="sheet">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="sheet2">
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>"
                                        alt="Product Image" class="img-thumbnail " style="width: 100px; height: auto;">
                                    <br>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>


        </div>
    </div>
</body>

</html>