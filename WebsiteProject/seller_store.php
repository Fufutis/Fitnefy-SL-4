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

// Get seller ID from the session
$seller_id = $_SESSION['user_id'];

// Determine the view type (all products or my products)
$view_type = isset($_GET['view']) && $_GET['view'] === 'my_products' ? 'my_products' : 'all_products';

// Fetch products based on the selected view
if ($view_type === 'my_products') {
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products WHERE seller_id = ?");
    $stmt->bind_param('i', $seller_id);
} else {
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products");
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch all products into an array
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <h1 class="mb-4"><?php echo $view_type === 'my_products' ? 'My Products' : 'All Products'; ?></h1>

        <!-- Switch Buttons -->
        <div class="mb-4">
            <a href="?view=all_products" class="btn <?php echo $view_type === 'all_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">All Products</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'seller'): ?>
                <a href="?view=my_products" class="btn <?php echo $view_type === 'my_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">My Products</a>
            <?php endif; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">
                <?php echo $view_type === 'my_products' ? 'You are not currently selling any products.' : 'No products available at the moment.'; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <!-- Display Product Image -->
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>

                                <!-- Buttons Row -->
                                <?php if ($view_type === 'my_products'): ?>
                                    <div class="d-grid gap-2">
                                        <!-- Edit Button -->
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-block">Edit</a>

                                        <!-- Delete Button -->
                                        <form action="delete_product.php" method="POST" class="mt-2">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <!-- Wishlist Button -->
                                        <button class="btn btn-warning me-2" onclick="addToWishlist(<?php echo $product['id']; ?>)">Wishlist</button>
                                        <!-- Add to Cart Button -->
                                        <button class="btn btn-success me-auto" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for AJAX -->
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
                    alert(response.message); // Show success or error message
                },
                error: function() {
                    alert('An error occurred while adding to the wishlist.');
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
                    alert(response.message); // Show success message
                },
                error: function() {
                    alert('An error occurred while adding to the cart.');
                }
            });
        }
    </script>
</body>

</html>