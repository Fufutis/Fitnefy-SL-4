<?php if (empty($sold_history)): ?>
    <div class="alert alert-info">No items have been sold yet.</div>
<?php else: ?>
    <?php foreach ($sold_history as $order): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                <p><strong>Total Revenue:</strong> $<?php echo number_format($order['total_revenue'], 2); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>