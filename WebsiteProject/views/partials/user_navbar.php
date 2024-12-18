<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/controllers/dashboard.php">User Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/views/seller_store_view.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/views/wishlist_view.php">Wishlist</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/views/order_history_view.php">Order History</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="btn btn-light" href="<?php echo BASE_URL; ?>/views/cart_view.php">Cart</a></li>
                <li class="nav-item"><a class="btn btn-danger" href="<?php echo BASE_URL; ?>/controllers/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>