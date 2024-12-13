<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}

include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Default filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'recent'; // Default: Recent
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc'; // Default: Descending

// Build SQL query with category filter and sorting
$query = "SELECT id, name, description, price, product_type, photo_blob FROM products WHERE 1=1";

// Filter by category
if (!empty($category)) {
    $query .= " AND product_type = ?";
}

// Sorting logic
if ($sort_by === 'recent') {
    $query .= " ORDER BY upload_timestamp " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
} elseif ($sort_by === 'price') {
    $query .= " ORDER BY price " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
}

// Prepare and execute query
$stmt = $conn->prepare($query);
if (!empty($category)) {
    $stmt->bind_param('s', $category);
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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Styled Filter and Sorting Section -->
        <div class="mb-4">
            <form method="GET" class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <!-- Title -->
                <div class="p-4 d-flex flex-column">
                    <h2 class="mb-0">All Products</h2>
                </div>

                <!-- Right-Aligned Dropdowns and Button -->
                <div class="d-flex gap-3">
                    <!-- Category Filter -->
                    <div class="d-flex flex-column">
                        <label for="category" class="form-label mb-1">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="e-book" <?php echo $category === 'e-book' ? 'selected' : ''; ?>>E-Book</option>
                            <option value="software" <?php echo $category === 'software' ? 'selected' : ''; ?>>Software</option>
                            <option value="template" <?php echo $category === 'template' ? 'selected' : ''; ?>>Template</option>
                            <option value="digital artwork" <?php echo $category === 'digital artwork' ? 'selected' : ''; ?>>Digital Artwork</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="d-flex flex-column">
                        <label for="sort_by" class="form-label mb-1">Sort By</label>
                        <select name="sort_by" id="sort_by" class="form-select">
                            <option value="recent" <?php echo $sort_by === 'recent' ? 'selected' : ''; ?>>Recent</option>
                            <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Price</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="d-flex flex-column">
                        <label for="sort_order" class="form-label mb-1">Sort Order</label>
                        <select name="sort_order" id="sort_order" class="form-select">
                            <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Descending</option>
                            <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mt-3">Apply</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Display Products -->
        <?php if (empty($products)): ?>
            <div class="alert alert-info">No products available with the selected criteria.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>"
                                class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>

                                <!-- Wishlist and Cart Buttons -->
                                <button class="btn btn-warning mt-2" onclick="addToWishlist(<?php echo $product['id']; ?>)">Add to Wishlist</button>
                                <a href="cart.php?action=add&product_id=<?php echo $product['id']; ?>" class="btn btn-success mt-2">Add to Cart</a>
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
    </script>
</body>

</html>