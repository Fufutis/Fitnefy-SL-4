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
$categories = [];

// Fetch categories dynamically for filtering
$category_query = "SELECT DISTINCT product_type FROM products";
$category_result = $conn->query($category_query);
if ($category_result) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row['product_type'];
    }
}

// Build the product query based on filters and sorting
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'recent';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';

$product_query = "SELECT p.id, p.name, p.description, p.price, p.product_type, p.photo_blob, u.username AS seller_username 
                  FROM products p 
                  LEFT JOIN users u ON p.seller_id = u.id 
                  WHERE 1=1";

// Apply category filter if set
if (!empty($category_filter)) {
    $product_query .= " AND p.product_type = ?";
}

// Apply view type filter
if ($view_type === 'my_products' && ($role === 'seller' || $role === 'both')) {
    $product_query .= " AND p.seller_id = ?";
}

// Add sorting
if ($sort_by === 'price') {
    $product_query .= " ORDER BY p.price";
} else {
    $product_query .= " ORDER BY p.upload_timestamp";
}
$product_query .= $sort_order === 'asc' ? " ASC" : " DESC";

$stmt = $conn->prepare($product_query);

// Bind parameters dynamically
$params = [];
$param_types = '';
if (!empty($category_filter)) {
    $param_types .= 's';
    $params[] = $category_filter;
}
if ($view_type === 'my_products' && ($role === 'seller' || $role === 'both')) {
    $param_types .= 'i';
    $params[] = $_SESSION['user_id'];
}

if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="bg-container">
        <?php for ($i = 0; $i < 3000; $i++): ?>
            <div class="tile"></div>
        <?php endfor; ?>
    </div>

    <div class="main-content">
        <div class="container mt-5 background">
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
                    <a href="?view=all_products" class="btn btn-design <?php echo $view_type === 'all_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">All Products</a>
                    <a href="?view=my_products" class="btn btn-design <?php echo $view_type === 'my_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">My Products</a>
                </div>
            <?php endif; ?>

            <!-- Sorting and Filtering -->
            <form class="d-flex mb-4" method="GET">
                <input type="hidden" name="view" value="<?php echo $view_type; ?>">

                <select name="category" class="form-select me-2 custom-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucfirst($cat)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="sort_by" class="form-select me-2 custom-select">
                    <option value="recent" <?php echo $sort_by === 'recent' ? 'selected' : ''; ?>>Recent</option>
                    <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Price</option>
                </select>

                <select name="sort_order" class="form-select me-2 custom-select">
                    <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Descending</option>
                    <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                </select>

                <button type="submit" class="btn btn-design">Filter</button>
            </form>

            <?php if (empty($products)): ?>
                <div class="alert sheet">
                    <?php echo $view_type === 'my_products' ? 'You are not currently selling any products.' : 'No products available at the moment.'; ?>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100 bg-item">
                                <div class="image-container">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" alt="Product Image">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                    <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
                                    <p class="card-text"><strong>Seller:</strong> <?php echo htmlspecialchars($product['seller_username']); ?></p>

                                    <?php if ($view_type === 'my_products'): ?>
                                        <div class="d-flex">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-design btn-in-cards me-2">Edit</a>
                                            <form action="delete_product.php" method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-design btn-in-cards" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex">
                                            <button class="btn btn-design btn-in-cards me-2" onclick="addToWishlist(<?php echo $product['id']; ?>)">Wishlist</button>
                                            <button class="btn btn-design btn-in-cards" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
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
                    displayMessage('Successfully added to your Cart', 'danger'); //SHHHH
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