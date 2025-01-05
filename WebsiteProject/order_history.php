<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your order history.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Determine view type for "both" users; sellers only see "sold"
$view_type = ($role === 'both')
    ? (isset($_GET['view']) && in_array($_GET['view'], ['bought', 'sold']) ? $_GET['view'] : 'bought')
    : (($role === 'seller') ? 'sold' : 'bought');

// Initialize histories
$order_history = [];
$sold_history = [];

// Fetch bought history (for user and both roles)
if (($role === 'user' || $role === 'both') && $view_type === 'bought') {
    $query = "
        SELECT 
            og.id AS order_id, 
            og.order_timestamp AS order_date, 
            SUM(p.price * o.quantity) AS total_price,
            p.id AS product_id,           /* FETCH product_id for image download */
            p.name AS product_name, 
            p.price AS product_price, 
            o.quantity
        FROM order_groups og
        JOIN orders o ON og.id = o.order_group_id
        JOIN products p ON o.product_id = p.id
        WHERE og.user_id = ?
        GROUP BY og.id, og.order_timestamp, p.id, p.name, p.price, o.quantity
        ORDER BY og.order_timestamp DESC
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
            'id' => $row['product_id'],       // STORE product_id
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'quantity' => $row['quantity'],
        ];
    }
    $stmt->close();
}

// Fetch sold history (for seller and both roles)
if (($role === 'seller' || $role === 'both') && $view_type === 'sold') {
    $query = "
        SELECT 
            o.order_group_id AS order_id, 
            og.order_timestamp AS order_date, 
            SUM(o.quantity * p.price) AS total_revenue,
            p.id AS product_id,            /* FETCH product_id for image download */
            p.name AS product_name, 
            p.price AS product_price, 
            o.quantity
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN order_groups og ON o.order_group_id = og.id
        WHERE p.seller_id = ?
        GROUP BY o.order_group_id, og.order_timestamp, p.id, p.name, p.price, o.quantity
        ORDER BY og.order_timestamp DESC

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
                'total_revenue' => $row['total_revenue'],
                'products' => [],
            ];
        }
        $sold_history[$order_id]['products'][] = [
            'id' => $row['product_id'],      // STORE product_id
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'quantity' => $row['quantity'],
        ];
    }
    $stmt->close();
}

$conn->close();
?>



<head>
    <title>Orders</title>
</head>

<body>
    <div class="bg-container">
        <?php

        for ($i = 0; $i < 3000; $i++) {
            echo '<div class="tile"></div>';
        }
        ?>
    </div>
    <main class="main-content">
        <div class="container mt-5 background">
            <h1 class="mb-4">Orders Sold</h1>

            <!-- Toggle Buttons: Only for "both" role -->
            <?php if ($role === 'both'): ?>
                <div class="mb-4">
                    <a href="?view=bought" class="btn btn-design <?php echo $view_type === 'bought' ? 'btn-primary' : 'btn-outline-primary'; ?>">Bought History</a>

                    <a href="?view=sold" class="btn btn-design <?php echo $view_type === 'sold' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold History</a>

                </div>
            <?php endif; ?>

            <!-- BOUGHT HISTORY (for user or both) -->
            <?php if ($view_type === 'bought' && ($role === 'user' || $role === 'both')): ?>
                <?php if (empty($order_history)): ?>
                    <div class="alert alert-info">You have no orders yet.</div>
                <?php else: ?>
                    <?php foreach ($order_history as $order_id => $order): ?>
                        <div class="card mb-4">
                            <div class="card-header sheet2">
                                <h5>Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                            </div>
                            <div class="card-body sheet2">
                                <h6>Products:</h6>
                                <ul class="list-group">
                                    <?php foreach ($order['products'] as $product): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center sheet2">
                                            <div>
                                                <!-- Display product name, quantity, price -->
                                                <?php echo htmlspecialchars($product['name']); ?>
                                                (<?php echo $product['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?>)

                                                <!-- Download Image Link -->
                                                <a href="download_image.php?id=<?php echo $product['id']; ?>"
                                                    class="btn btn-sm btn-secondary ms-2 sheet">
                                                    Download Image
                                                </a>
                                            </div>
                                            <span>
                                                $<?php echo number_format($product['quantity'] * $product['price'], 2); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>

            <!-- SOLD HISTORY (for seller or both) -->
            <?php if ($view_type === 'sold' && ($role === 'seller' || $role === 'both')): ?>
                <?php if (empty($sold_history)): ?>
                    <div class="alert alert-info">No items have been sold yet.</div>
                <?php else: ?>
                    <?php foreach ($sold_history as $order_id => $order): ?>
                        <div class="card mb-4">
                            <div class="card-header sheet2">
                                <h5>Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                                <p><strong>Total Revenue:</strong> $<?php echo number_format($order['total_revenue'], 2); ?></p>
                            </div>
                            <div class="card-body sheet2">
                                <h6>Products Sold:</h6>
                                <ul class="list-group">
                                    <?php foreach ($order['products'] as $product): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center sheet2">
                                            <div>
                                                <!-- Display product name, quantity, price -->
                                                <?php echo htmlspecialchars($product['name']); ?>
                                                (<?php echo $product['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?>)

                                                <!-- Download Image Link -->
                                                <a href="download_image.php?id=<?php echo $product['id']; ?>"
                                                    class="btn btn-sm btn-secondary ms-2 sheet">
                                                    Download Image
                                                </a>
                                            </div>
                                            <span>
                                                $<?php echo number_format($product['quantity'] * $product['price'], 2); ?>
                                            </span>
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