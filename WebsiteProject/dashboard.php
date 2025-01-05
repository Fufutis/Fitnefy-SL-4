<?php
include("dashboard_model.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Role-Specific Navigation -->
        <?php if ($role === 'seller' || $role === 'both'): ?>
            <!-- Seller/Both Navigation -->
            <div class="mb-4">
                <a href="?view=sold_items" class="btn <?php echo $view_type === 'sold_items' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sold Items</a>
                <a href="?view=my_products" class="btn <?php echo $view_type === 'my_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">My Products</a>
                <?php if ($role === 'both'): ?>
                    <a href="?view=all_products" class="btn <?php echo $view_type === 'all_products' ? 'btn-primary' : 'btn-outline-primary'; ?>">All Products</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($view_type === 'all_products'): ?>
            <!-- Sorting and Filtering for All Products -->
            <?php
            // Fetch categories dynamically from the products table
            $categories = [];
            $category_query = "SELECT DISTINCT product_type FROM products";
            $result = $conn->query($category_query);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $categories[] = $row['product_type'];
                }
            }
            ?>

            <form class="d-flex mb-4" method="GET">
                <input type="hidden" name="view" value="all_products">

                <!-- Dynamic Category Dropdown -->
                <select name="category" class="form-select me-2">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucfirst($cat)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Sort By Dropdown -->
                <select name="sort_by" class="form-select me-2">
                    <option value="recent" <?php echo $sort_by === 'recent' ? 'selected' : ''; ?>>Recent</option>
                    <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Price</option>
                </select>

                <!-- Sort Order Dropdown -->
                <select name="sort_order" class="form-select me-2">
                    <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Descending</option>
                    <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                </select>

                <!-- Filter Button -->
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        <?php endif; ?>


        <!-- Display Sold Items -->
        <?php if ($view_type === 'sold_items' && ($role === 'seller' || $role === 'both')): ?>
            <h2 class="mb-4">Sold Items</h2>
            <?php if (empty($sold_items)): ?>
                <div class="alert alert-info">No items have been sold yet.</div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sold_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($item['total_price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($item['order_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Display My Products -->
        <?php if ($view_type === 'my_products' && ($role === 'seller' || $role === 'both')): ?>
            <h2 class="mb-4">My Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Display All Products -->
        <?php if ($view_type === 'all_products' && ($role === 'user' || $role === 'both')): ?>
            <h2 class="mb-4">All Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="image-container">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" alt="Product Image">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
                                <button class="btn btn-warning mt-2" onclick="addToWishlist(<?php echo $product['id']; ?>)">Add to Wishlist</button>
                                <button class="btn btn-success mt-2" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- AJAX Scripts for Wishlist and Cart -->
    <script>
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
                    displayMessage(response.message, 'success');
                },
                error: function() {
                    displayMessage('An error occurred while adding to the cart.', 'danger');
                }
            });
        }

        function addToWishlist(productId) {
            $.ajax({
                url: 'wishlist.php',
                type: 'GET',
                data: {
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    displayMessage(response.message, 'success');
                },
                error: function() {
                    displayMessage('An error occurred while adding to the wishlist.', 'danger');
                }
            });
        }

        function displayMessage(message, type) {
            const alertBox = `
                <div class="alert alert-${type} fixed-alert" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 90%; max-width: 500px; text-align: center;">
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