<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in and has the seller or both role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'both')) {
    $_SESSION['message'] = "Access denied. Only sellers can view this page.";
    header("Location: index.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch sold items for the seller
$stmt = $conn->prepare("
    SELECT 
        p.name AS product_name,
        p.price AS product_price,
        SUM(o.quantity) AS total_quantity_sold,
        SUM(o.total_price) AS total_revenue,
        MAX(og.created_at) AS last_sold_date
    FROM orders o
    JOIN order_groups og ON o.order_group_id = og.id
    JOIN products p ON o.product_id = p.id
    WHERE p.seller_id = ?
    GROUP BY o.product_id
    ORDER BY last_sold_date DESC
");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$result = $stmt->get_result();

$sold_items = [];
while ($row = $result->fetch_assoc()) {
    $sold_items[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Sold Items</h1>

        <?php if (empty($sold_items)): ?>
            <div class="alert alert-info">You haven’t sold any items yet.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (Per Item)</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Revenue</th>
                        <th>Date of Last Sale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sold_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['total_quantity_sold']); ?></td>
                            <td>$<?php echo number_format($item['total_revenue'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['last_sold_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>