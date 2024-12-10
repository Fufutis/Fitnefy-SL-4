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

// Fetch products from the database
$stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
$stmt->bind_param('i', $seller_id);
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
    <title>My Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">My Store</h1>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">You have not added any products yet.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['photo']); ?>" class="card-img-top" alt="Product Image">
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
    </div>
</body>
</html>
