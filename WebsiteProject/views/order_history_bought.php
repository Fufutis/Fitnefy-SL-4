<?php if (empty($order_history)): ?>
    <div class="alert alert-info">You have no orders yet.</div>
<?php else: ?>
    <?php foreach ($order_history as $order): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>