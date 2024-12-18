<?php include_once __DIR__ . '/../partials/header.php'; ?>
<?php include_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Order History</h1>

    <?php if ($role === 'both'): ?>
        <div class="mb-4">
            <a href="?view=bought" class="btn <?php echo $view_type === 'bought' ? 'btn-primary' : 'btn-outline-primary'; ?>">Bought History</a>
            <a href="?view=sold" class="btn <?php echo $view_type === 'sold' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold History</a>
        </div>
    <?php endif; ?>

    <?php
    if ($view_type === 'bought') {
        include_once __DIR__ . '/order_history_bought.php';
    } elseif ($view_type === 'sold') {
        include_once __DIR__ . '/order_history_sold.php';
    }
    ?>
</div>