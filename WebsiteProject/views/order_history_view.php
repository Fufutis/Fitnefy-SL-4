<?php
include_once __DIR__ . '/partials/header.php';
include_once __DIR__ . '/partials/navbar.php';

// Debugging: Simulate correct role if needed
$role = 'both'; // Temporary for testing; replace with actual role logic

// Initialize and sanitize $view_type
$view_type = isset($_GET['view']) ? $_GET['view'] : '';
$allowed_views = ['bought', 'sold'];
$view_type = in_array($view_type, $allowed_views) ? $view_type : '';

// Debug outputs
var_dump($role);
var_dump($view_type);
?>

<div class="container mt-5">
    <h1 class="mb-4">Order History</h1>

    <?php if ($role === 'both'): ?>
        <div class="mb-4">
            <a href="?view=bought" class="btn <?php echo $view_type === 'bought' ? 'btn-primary' : 'btn-outline-primary'; ?>">Bought History</a>
            <a href="?view=sold" class="btn <?php echo $view_type === 'sold' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold History</a>
        </div>
    <?php else: ?>
        <p>No access to view both histories.</p>
    <?php endif; ?>

    <?php if ($view_type === 'bought'): ?>
        <?php include_once __DIR__ . '/order_history_bought.php'; ?>
    <?php elseif ($view_type === 'sold'): ?>
        <?php include_once __DIR__ . '/order_history_sold.php'; ?>
    <?php else: ?>
        <p>Select a view to see your history.</p>
    <?php endif; ?>
</div>