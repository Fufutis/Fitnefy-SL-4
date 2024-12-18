<?php include("partials/header.php"); ?>
<?php include("partials/navbar.php"); ?>
<h1>Seller Dashboard</h1>
<?php if (!empty($sold_items)): ?>
    <h2>Sold Items</h2>
    <!-- Render Sold Items Table -->
<?php else: ?>
    <p>No sold items yet.</p>
<?php endif; ?>