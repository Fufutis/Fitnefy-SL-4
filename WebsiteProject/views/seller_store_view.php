<?php
include_once __DIR__ . '/partials/header.php';
include_once __DIR__ . '/partials/navbar.php';

// Initialize $view_type based on query parameters or default to 'all_products'
$view_type = isset($_GET['view']) ? $_GET['view'] : 'all_products';

// Initialize $role (if not already passed from the controller)
$role = $_SESSION['role'] ?? 'user';

// Sanitize $view_type to allow only valid options
$allowed_views = ['all_products', 'my_products'];
$view_type = in_array($view_type, $allowed_views) ? $view_type : 'all_products';
?>

<div class="container mt-5">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php
            echo htmlspecialchars($_SESSION['message']);
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <h1 class="mb-4"><?php echo $view_type === 'my_products' ? 'My Products' : 'All Products'; ?></h1>

    <!-- Toggle Buttons for "both" roles -->
    <?php if ($role === 'both'): ?>
        <div class="mb-4">
            <a href="?view=all_products" class="btn <?php echo $view_type === 'all_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">All Products</a>
            <a href="?view=my_products" class="btn <?php echo $view_type === 'my_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">My Products</a>
        </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <?php echo $view_type === 'my_products' ? 'You are not currently selling any products.' : 'No products available at the moment.'; ?>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>

                            <!-- Conditional Buttons -->
                            <?php if ($view_type === 'my_products'): ?>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo BASE_URL; ?>/controllers/edit_product_controller.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Edit</a>
                                    <form action="<?php echo BASE_URL; ?>/controllers/delete_product_controller.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-warning" onclick="addToWishlist(<?php echo $product['id']; ?>)">Wishlist</button>
                                    <button class="btn btn-success" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function addToWishlist(productId) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/controllers/wishlist_controller.php',
            type: 'GET',
            data: {
                product_id: productId
            },
            success: function(response) {
                alert(response.message);
            },
            error: function() {
                alert('Error adding to wishlist.');
            }
        });
    }

    function addToCart(productId) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/controllers/cart_controller.php',
            type: 'GET',
            data: {
                action: 'add',
                product_id: productId
            },
            success: function(response) {
                alert(response.message);
            },
            error: function() {
                alert('Error adding to cart.');
            }
        });
    }
</script>

<?php include_once __DIR__ . '/partials/footer.php'; ?>
