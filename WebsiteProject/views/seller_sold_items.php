<?php include_once __DIR__ . '/partials/header.php'; ?>
<?php include_once __DIR__ . '/partials/navbar.php'; ?>

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

<?php include_once __DIR__ . '/partials/footer.php'; ?>