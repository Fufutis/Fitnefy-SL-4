<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "Please log in to view your store.";
    header("Location: index.php");
    exit;
}

// Determine user role and set the default view type
$role = $_SESSION['role'] ?? 'user';
$view_type = isset($_GET['view']) ? $_GET['view'] : 'all_products';

// Restrict views based on roles
if ($role === 'user') {
    $view_type = 'all_products'; // Users can only see "All Products"
} elseif ($role === 'seller') {
    $view_type = 'my_products'; // Sellers can only see "My Products"
} elseif ($role === 'both') {
    // Both can toggle between "All Products" and "My Products"
    if (!in_array($view_type, ['all_products', 'my_products'])) {
        $view_type = 'all_products'; // Default view
    }
}

// Fetch products based on the view type
$products = [];
if ($view_type === 'my_products') {
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products WHERE seller_id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
} else {
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products");
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <!-- Tiled Background -->
    <div class="bg-container">
        <?php for ($i = 0; $i < 3000; $i++): ?>
            <div class="tile"></div>
        <?php endfor; ?>
    </div>
    <div class="main-content">
        <div class="container mt-5 background">
            <!-- Display Success/Error Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert sheet">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <h1 class="mb-4"><?php echo $view_type === 'my_products' ? 'My Products' : 'All Products'; ?></h1>

            <!-- Toggle Buttons for Both Role -->
            <?php if ($role === 'both'): ?>
                <div class="mb-4">
                    <a href="?view=all_products"
                        class="btn btn-design <?php echo $view_type === 'all_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        All Products
                    </a>
                    <a href="?view=my_products"
                        class="btn btn-design <?php echo $view_type === 'my_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        My Products
                    </a>
                </div>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <div class="alert sheet">
                    <?php echo $view_type === 'my_products'
                        ? 'You are not currently selling any products.'
                        : 'No products available at the moment.';
                    ?>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100 bg-item">
                                <!-- Product Image -->
                                <div class="image-container">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>"
                                        class="card-img-top"
                                        alt="Product Image" />
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p class="card-text">
                                        <strong>Price:</strong> $
                                        <?php echo htmlspecialchars($product['price']); ?>
                                    </p>
                                    <p class="card-text">
                                        <strong>Type:</strong>
                                        <?php echo htmlspecialchars($product['product_type']); ?>
                                    </p>

                                    <!-- If this is MY Products (Seller), show edit/delete -->
                                    <?php if ($view_type === 'my_products'): ?>
                                        <div class="d-flex">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                                class="btn btn-design btn-in-cards me-2">
                                                Edit
                                            </a>
                                            <form action="delete_product.php" method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-design btn-in-cards" onclick="return confirm('Are you sure you want to delete this product?');">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                    <?php elseif ($role === 'user' || $role === 'both'): ?>
                                        <!-- If user or both => Show wishlist / add to cart -->
                                        <div class="d-flex  align-items-center">
                                            <button class="btn btn-design btn-in-cards me-2"
                                                onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                                Wishlist
                                            </button>
                                            <button class="btn btn-design btn-in-cards"
                                                onclick="addToCart(<?php echo $product['id']; ?>)">
                                                Add to Cart
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function addToWishlist(productId) {
            $.ajax({
                url: 'wishlist.php',
                type: 'GET',
                data: {
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    displayMessage(response.message, response.success ? 'success' : 'danger');
                },
                error: function() {
                    displayMessage('An error occurred while adding to the wishlist.', 'danger');
                }
            });
        }

        function addToCart(productId) {
            $.ajax({
                url: 'cart_action.php',
                type: 'GET',
                data: {
                    action: 'add',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    displayMessage(response.message, response.success ? 'success' : 'danger');
                },
                error: function() {
                    displayMessage('An error occurred while adding to the cart.', 'danger');
                }
            });
        }

        function displayMessage(message, type) {
            const alertBox = `
                <div class="alert sheet alert-${type} fixed-alert" style="opacity: 0.9; font-weight: bold" role="alert">
                    ${message}
                </div>`;
            document.body.insertAdjacentHTML('beforeend', alertBox);
            setTimeout(() => {
                const alert = document.querySelector('.fixed-alert');
                if (alert) alert.remove();
            }, 3000);
        }
    </script>
</body>

</html>