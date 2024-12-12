<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "Please log in to view your store.";
    header("Location: index.php");
    exit;
}

// Get seller ID from the session
$seller_id = $_SESSION['user_id'];

// Fetch all products from the database
$stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products");
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
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">All Products</h1>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">No products available at the moment.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <!-- Display Product Image -->
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>" 
                                 class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
                                <!-- Download Link -->
                                <a href="download_image.php?id=<?php echo $product['id']; ?>" 
                                class="btn btn-primary mt-2">Download Image</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
