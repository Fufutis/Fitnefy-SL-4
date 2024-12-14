<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_group_id'])) {
    header("Location: cart_view.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_group_id = $_SESSION['order_group_id'];

// Fetch order details
$stmt = $conn->prepare("
    SELECT 
        p.name, 
        o.quantity, 
        p.price, 
        (o.quantity * p.price) AS total 
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.order_group_id = ?
");
$stmt->bind_param('i', $order_group_id);
$stmt->execute();
$result = $stmt->get_result();

$order_items = [];
$total_price = 0;
while ($row = $result->fetch_assoc()) {
    $total_price += $row['total'];
    $order_items[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Order Confirmation</h1>
        <p>Thank you for your purchase! Here are your order details:</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['total'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total Price: $<?php echo number_format($total_price, 2); ?></h3>
        <a href="dashboard.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
</body>

</html>