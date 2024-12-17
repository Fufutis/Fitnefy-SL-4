<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your history.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Determine the view: 'order_history' or 'sold_history'
$view = isset($_GET['view']) && in_array($_GET['view'], ['order_history', 'sold_history'])
    ? $_GET['view']
    : 'order_history';

// Initialize data arrays
$order_history = [];
$sold_history = [];

// Fetch order history for 'user' or 'both'
if (($role === 'user' || $role === 'both') && $view === 'order_history') {
    $query = "
        SELECT 
            og.id AS order_id, 
            og.created_at AS order_date, 
            SUM(p.price * o.quantity) AS total_price,
            p.name AS product_name, 
            p.price AS product_price, 
            o.quantity
        FROM order_groups og
        JOIN orders o ON og.id = o.order_group_id
        JOIN products p ON o.product_id = p.id
        WHERE og.user_id = ?
        GROUP BY og.id, og.created_at, p.name, p.price, o.quantity
        ORDER BY og.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        if (!isset($order_history[$order_id])) {
            $order_history[$order_id] = [
                'order_date' => $row['order_date'],
                'total_price' => $row['total_price'],
                'products' => [],
            ];
        }
        $order_history[$order_id]['products'][] = [
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'quantity' => $row['quantity'],
        ];
    }
    $stmt->close();
}

// Fetch sold history for 'seller' or 'both'
if (($role === 'seller' || $role === 'both') && $view === 'sold_history') {
    $query = "
        SELECT 
            og.id AS order_id, 
            og.created_at AS order_date, 
            SUM(p.price * o.quantity) AS total_price,
            p.name AS product_name, 
            p.price AS product_price, 
            o.quantity
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN order_groups og ON o.order_group_id = og.id
        WHERE p.seller_id = ?
        GROUP BY og.id, og.created_at, p.name, p.price, o.quantity
        ORDER BY og.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        if (!isset($sold_history[$order_id])) {
            $sold_history[$order_id] = [
                'order_date' => $row['order_date'],
                'total_price' => $row['total_price'],
                'products' => [],
            ];
        }
        $sold_history[$order_id]['products'][] = [
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'quantity' => $row['quantity'],
        ];
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order & Sold History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">History</h1>

        <?php if ($role === 'both'): ?>
            <!-- Switch Buttons for "both" role -->
            <div class="mb-4">
                <a href="?view=order_history" class="btn <?php echo $view === 'order_history' ? 'btn-primary' : 'btn-outline-primary'; ?>">Order History</a>
                <a href="?view=sold_history" class="btn <?php echo $view === 'sold_history' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold History</a>
            </div>
        <?php endif; ?>

        <?php if ($view === 'order_history'): ?>
            <h2 class="mb-4">Your Order History</h2>
            <?php if (empty($order_history)): ?>
                <div class="alert alert-info">You have no orders yet.</div>
            <?php else: ?>
                <?php foreach ($order_history as $order_id => $order): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php foreach ($order['products'] as $product): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($product['name']); ?> (<?php echo $product['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?>)</span>
                                        <span>$<?php echo number_format($product['quantity'] * $product['price'], 2); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($view === 'sold_history'): ?>
            <h2 class="mb-4">Your Sold History</h2>
            <?php if (empty($sold_history)): ?>
                <div class="alert alert-info">No products have been sold yet.</div>
            <?php else: ?>
                <?php foreach ($sold_history as $order_id => $order): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Total Earnings:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php foreach ($order['products'] as $product): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($product['name']); ?> (<?php echo $product['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?>)</span>
                                        <span>$<?php echo number_format($product['quantity'] * $product['price'], 2); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
