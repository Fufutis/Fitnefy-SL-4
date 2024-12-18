<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/partials/header.php';
include_once __DIR__ . '/partials/navbar.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your wishlist.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.description, p.price, p.photo_blob 
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Wishlist</h1>

        <?php if (empty($wishlist_items)): ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?></p>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-success" onclick="addToCart(<?php echo $item['id']; ?>)">Add to Cart</button>
                                    <button class="btn btn-danger" onclick="removeFromWishlist(<?php echo $item['id']; ?>)">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        location.reload(); // Reload the page to update the wishlist
                    }
                },
                error: function() {
                    alert('An error occurred while removing the item from the wishlist.');
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