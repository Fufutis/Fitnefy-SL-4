<?php
include_once __DIR__ . '/partials/header.php';
include_once __DIR__ . '/partials/navbar.php';

// Fetch role and initialize view
$role = $_SESSION['role'] ?? 'user'; // Replace with actual role logic
$default_view = 'sold'; // Default to 'sold'
$view_type = isset($_GET['view']) ? $_GET['view'] : $default_view;

// Allowed views
$allowed_views = ['bought', 'sold'];
$view_type = in_array($view_type, $allowed_views) ? $view_type : $default_view;
?>

<div class="container mt-5">
    <h1 class="mb-4">Order History</h1>

    <?php if ($role === 'both'): ?>
        <!-- Buttons to toggle view dynamically -->
        <div class="mb-4">

            <button id="btn-sold" class="btn <?php echo $view_type === 'sold' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold History</button>
            <button id="btn-bought" class="btn <?php echo $view_type === 'bought' ? 'btn-primary' : 'btn-outline-primary'; ?>">Bought History</button>
            
        </div>
    <?php endif; ?>

    <!-- History Content -->
    <div id="history-content">
        <?php if ($view_type === 'bought'): ?>
            <?php include_once __DIR__ . '/order_history_bought.php'; ?>
        <?php elseif ($view_type === 'sold'): ?>
            <?php include_once __DIR__ . '/order_history_sold.php'; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // JavaScript to toggle view dynamically
    document.addEventListener('DOMContentLoaded', function() {
        const btnBought = document.getElementById('btn-bought');
        const btnSold = document.getElementById('btn-sold');
        const historyContent = document.getElementById('history-content');

        btnBought.addEventListener('click', function() {
            fetchContent('bought');
        });

        btnSold.addEventListener('click', function() {
            fetchContent('sold');
        });

        function fetchContent(viewType) {
            fetch(`order_history_${viewType}.php`)
                .then(response => response.text())
                .then(data => {
                    historyContent.innerHTML = data;

                    // Update button states
                    btnBought.classList.toggle('btn-primary', viewType === 'bought');
                    btnBought.classList.toggle('btn-outline-primary', viewType !== 'bought');
                    btnSold.classList.toggle('btn-primary', viewType === 'sold');
                    btnSold.classList.toggle('btn-outline-primary', viewType !== 'sold');
                })
                .catch(error => console.error('Error fetching content:', error));
        }
    });
</script>