<?php include("partials/header.php"); ?>
<?php include("partials/navbar.php"); ?>
<h1>Both Dashboard</h1>
<a href="?view=sold_items" class="btn btn-primary">Sold Items</a>
<a href="?view=my_products" class="btn btn-primary">My Products</a>
<a href="?view=all_products" class="btn btn-primary">All Products</a>
<?php if ($view_type === 'sold_items'): ?>
    <h2>Sold Items</h2>
    <!-- Render Sold Items Table -->
<?php elseif ($view_type === 'my_products'): ?>
    <h2>My Products</h2>
    <!-- Render My Products List -->
<?php else: ?>
    <h2>All Products</h2>
    <?php include("product_list.php"); ?>
<?php endif; ?>