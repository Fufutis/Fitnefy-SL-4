<?php
include_once __DIR__ . '/partials/header.php';
include_once __DIR__ . '/partials/navbar.php';
?>
>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Wishlist</h1>

        <?php if (empty($wishlist_items)): ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($wishlist_items as $item): ?>
                    <?php include __DIR__ . '/wishlist_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- AJAX Scripts -->
    <script>
        function removeFromWishlist(productId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/controllers/wishlist_controller.php',
                type: 'POST',
                data: {
                    action: 'remove',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('An error occurred while removing the item.');
                }
            });
        }

        function addToCart(productId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/controllers/cart_controller.php',
                type: 'POST',
                data: {
                    action: 'add',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                },
                error: function() {
                    alert('An error occurred while adding the item to the cart.');
                }
            });
        }
    </script>
</body>

</html>