<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your wishlist.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$stmt = $conn->prepare("SELECT products.id, products.name, products.description, products.price, products.photo_blob 
                        FROM wishlist 
                        JOIN products ON wishlist.product_id = products.id 
                        WHERE wishlist.user_id = ?");
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="main-content">
    <!-- Tiled Background -->
    <div class="bg-container">
        <?php
        // Generate 3000 tiles dynamically
        for ($i = 0; $i < 3000; $i++) {
            echo '<div class="tile"></div>';
        }
        ?>
    </div>

    <div class="container mt-5">
        <h1 class="z">Your Wishlist</h1>
        <?php if (empty($wishlist_items)): ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="col">
                        <div class="card h-100 bg-item">
                            <div class="image-container">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>" alt="Product Image">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?></p>
                                <!-- Add to Cart Button -->
                                <!-- Replaced "btn-success" with your custom .btn-design class -->
                                <button
                                    class="btn btn-in-cards add-to-cart"
                                    data-product-id="<?php echo $item['id']; ?>">
                                    Add to Cart
                                </button>
                                <!-- Remove Button -->
                                <!-- Replaced "btn-danger" with your custom .btn-wish class -->
                                <button
                                    class="btn remove-from-wishlist btn-in-cards"
                                    data-product-id="<?php echo $item['id']; ?>">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for AJAX -->
    <script>
        $(document).ready(function() {
            // Add to Cart functionality
            $('.add-to-cart').click(function() {
                const productId = $(this).data('product-id');
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
            });

            // Remove from Wishlist functionality
            $('.remove-from-wishlist').click(function() {
                const productId = $(this).data('product-id');
                $.ajax({
                    url: 'wishlist_action.php',
                    type: 'POST',
                    data: {
                        action: 'remove',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while removing the item from the wishlist.');
                    }
                });
            });
        });
    </script>
</body>

</html>